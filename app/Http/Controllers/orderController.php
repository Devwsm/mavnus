<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\product;
use App\Models\ProductVariant;
use App\Exceptions\RajaOngkirException;
use App\Services\RajaOngkirService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class orderController extends Controller
{
    //
    public function dashboardIndex(Request $request)
    {
        $status = $request->query('status');

        $orders = Order::with(['items.product.images'])
            ->when($status, fn($query) => $query->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        // Dihitung terpisah dari data yang tampil di halaman (yang cuma 15
        // per page & bisa lagi difilter) supaya angkanya selalu total asli.
        $statusCounts = [
            'pending'    => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'shipped'    => Order::where('status', 'shipped')->count(),
            'completed'  => Order::where('status', 'completed')->count(),
        ];

        return view('pages.dashboard.orders', compact('orders', 'status', 'statusCounts'));
    }

    public function dashboardShow(Order $order)
    {
        $order->load('items.product.images');
        return view('pages.dashboard.order-detail', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,completed,cancelled',
        ]);
        $order->update(['status' => $validated['status']]);
        return redirect()
            ->route('dashboard.orders.show', $order)
            ->with('success', 'Status pesanan berhasil diperbarui.');
    }

    public function checkout()
    {
        $cartItems = CartItem::where('session_id', session()->getId())
            ->with(['product.images', 'variant'])
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('home')->with('error', 'Keranjang kamu masih kosong.');
        }

        return view('pages.checkout', compact('cartItems'));
    }

    public function store(Request $request, RajaOngkirService $rajaOngkir)
    {
        // shipping_cost SENGAJA gak divalidasi/dipakai dari form: angka itu cuma buat
        // tampilan di browser. Ongkir asli dihitung ulang di server (lihat di bawah).
        $validated = $request->validate([
            'customer_name'      => 'required|string|max:255',
            'customer_phone'     => ['required', 'regex:/^[0-9]{9,15}$/'],
            'customer_address'   => 'required|string',
            'destination_id'     => 'required|integer',
            'destination_label'  => 'required|string',
            'shipping_courier'   => 'required|string',
            'shipping_service'   => 'required|string',
        ], [
            'customer_name.required'    => 'Nama wajib diisi.',
            'customer_phone.required'   => 'Nomor HP wajib diisi.',
            'customer_phone.regex'      => 'Nomor HP harus berupa angka saja (9-15 digit), tanpa spasi atau simbol.',
            'customer_address.required' => 'Alamat wajib diisi.',
            'destination_id.required'   => 'Silakan pilih kecamatan/kota tujuan.',
            'shipping_courier.required' => 'Silakan pilih kurir pengiriman.',
            'shipping_service.required' => 'Silakan pilih layanan pengiriman.',
        ]);

        $sessionId = session()->getId();

        // ---- 1. Hitung ongkir di server (jangan percaya angka dari browser) ----
        $previewCart = CartItem::where('session_id', $sessionId)->with('product')->get();

        if ($previewCart->isEmpty()) {
            return redirect()->route('home')->with('error', 'Keranjang kamu masih kosong.');
        }

        $quotedWeight = $this->cartWeight($previewCart->sum(fn($item) => $item->product->weight * $item->quantity));

        try {
            $options = $rajaOngkir->calculateCost((int) $validated['destination_id'], $quotedWeight);
        } catch (RajaOngkirException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        $selected = collect($options)->first(
            fn($opt) => strcasecmp((string) ($opt['name'] ?? ''), $validated['shipping_courier']) === 0
                && strcasecmp((string) ($opt['service'] ?? ''), $validated['shipping_service']) === 0
        );

        if (! $selected) {
            return back()->withInput()->with('error', 'Layanan pengiriman yang dipilih tidak tersedia untuk tujuan ini. Silakan pilih ulang kurir.');
        }

        $shippingCost = (int) $selected['cost'];

        // ---- 2. Buat pesanan. Semua pengecekan stok + pengurangan stok ada DI DALAM
        // transaksi dan memakai row lock, jadi dua checkout bersamaan gak bisa
        // sama-sama lolos cek lalu bikin stok minus. ----
        try {
            $order = DB::transaction(function () use ($validated, $sessionId, $quotedWeight, $selected, $shippingCost) {
                // Kunci baris cart milik session ini dulu. Efek sampingnya: kalau tombol
                // "Buat Pesanan" kepencet 2x, request kedua nunggu, lalu ketemu cart kosong
                // (gak bikin pesanan ganda).
                $cartItems = CartItem::where('session_id', $sessionId)
                    ->orderBy('id_cart_item')
                    ->lockForUpdate()
                    ->get();

                if ($cartItems->isEmpty()) {
                    throw new \DomainException('Keranjang kamu masih kosong.');
                }

                // Urutan kunci selalu sama (produk, lalu varian, diurutkan by id)
                // biar dua transaksi gak saling nunggu (deadlock).
                $products = product::with('images')
                    ->whereIn('id_product', $cartItems->pluck('product_id')->unique())
                    ->orderBy('id_product')
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id_product');

                $variantIds = $cartItems->pluck('variant_id')->filter()->unique();
                $variants = $variantIds->isEmpty()
                    ? collect()
                    : ProductVariant::whereIn('id_variant', $variantIds)
                    ->orderBy('id_variant')
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id_variant');

                // Hitung subtotal & berat dari data produk yang sudah dikunci
                $subtotal = 0;
                $totalWeight = 0;
                foreach ($cartItems as $item) {
                    $prod = $products->get($item->product_id);
                    if (! $prod) {
                        throw new \DomainException('Ada produk di keranjang yang sudah tidak tersedia.');
                    }
                    $subtotal += $prod->price * $item->quantity;
                    $totalWeight += $prod->weight * $item->quantity;
                }

                // Kalau isi cart berubah sejak ongkir dihitung, ongkirnya udah gak valid
                if ($this->cartWeight($totalWeight) !== $quotedWeight) {
                    throw new \DomainException('Isi keranjang berubah. Silakan pilih ulang kurir pengiriman.');
                }

                $order = Order::create([
                    'order_number'      => Order::generateOrderNumber(),
                    // Kalau lagi login, order otomatis kesambung ke akun & muncul di riwayat pesanan.
                    // Kalau checkout sebagai guest, tetep null - gak masalah, order tetep kebuat normal.
                    'user_id'           => Auth::id(),
                    'customer_name'     => $validated['customer_name'],
                    'customer_phone'    => $validated['customer_phone'],
                    'customer_address'  => $validated['customer_address'] . ', ' . $validated['destination_label'],
                    // Nama kurir/layanan diambil dari hasil API, bukan dari input browser
                    'shipping_courier'  => $selected['name'],
                    'shipping_service'  => $selected['service'],
                    'shipping_cost'     => $shippingCost,
                    'subtotal'          => $subtotal,
                    'total'             => $subtotal + $shippingCost,
                    'total_weight'      => $totalWeight,
                    'status'            => 'pending',
                    'payment_status'    => 'unpaid',
                ]);

                foreach ($cartItems as $item) {
                    $prod = $products->get($item->product_id);
                    $variant = $item->variant_id ? $variants->get($item->variant_id) : null;

                    if ($item->variant_id && (! $variant || $variant->product_id !== $prod->id_product)) {
                        throw new \DomainException("Varian {$prod->name} di keranjang tidak valid. Silakan hapus lalu tambahkan lagi.");
                    }

                    // Cek stok TERBARU (sudah terkunci). Dicek sambil dikurangi satu-satu,
                    // jadi kalau ada 2 baris cart untuk produk yang sama, totalnya tetap kehitung.
                    $available = $variant ? $variant->stock : (int) $prod->stock;
                    if ($item->quantity > $available) {
                        throw new \DomainException("Stok {$prod->name} tidak mencukupi. Sisa stok: {$available}.");
                    }

                    // Snapshot foto produk (biar riwayat order gak ikut berubah kalau produk diedit/dihapus)
                    $snapshotImagePath = null;
                    $originalImage = $prod->images->first();
                    if ($originalImage && Storage::disk('public')->exists($originalImage->image_path)) {
                        $snapshotImagePath = 'orders/' . $order->order_number . '/' . Str::uuid() . '.webp';
                        Storage::disk('public')->makeDirectory('orders/' . $order->order_number);
                        Storage::disk('public')->copy($originalImage->image_path, $snapshotImagePath);
                    }

                    OrderItem::create([
                        'order_id'      => $order->id_order,
                        'product_id'    => $item->product_id,
                        'variant_id'    => $item->variant_id,
                        'product_name'  => $prod->name,
                        'product_image' => $snapshotImagePath,
                        'variant_label' => $variant->label ?? null,
                        'weight'        => $prod->weight,
                        'price'         => $prod->price,
                        'quantity'      => $item->quantity,
                        'subtotal'      => $prod->price * $item->quantity,
                    ]);

                    // Kurangi stok - clothes dari variant-nya, accessories langsung dari
                    // kolom stock di produknya (accessories gak punya varian ukuran).
                    if ($variant) {
                        $variant->decrement('stock', $item->quantity);
                    } else {
                        $prod->decrement('stock', $item->quantity);
                    }
                    $prod->syncActiveStatus();
                }

                CartItem::where('session_id', $sessionId)->delete();

                return $order;
            }, 3); // maks 3x percobaan kalau kena deadlock database
        } catch (\DomainException $e) {
            // Pesan sengaja ditampilkan apa adanya (isinya stok/keranjang, bukan data sensitif)
            return redirect()->route('order.checkout')->with('error', $e->getMessage());
        }

        // Catat di session bahwa pesanan ini dibuat oleh browser ini. Dipakai buat
        // ngizinin halaman sukses (lihat success()) - penting buat pembeli guest.
        $owned = $request->session()->get('owned_orders', []);
        $owned[] = $order->order_number;
        $request->session()->put('owned_orders', array_slice(array_values(array_unique($owned)), -20));

        return redirect()->route('order.success', $order)->with('success', 'Pesanan berhasil dibuat.');
    }

    public function success(Request $request, Order $order)
    {
        // Halaman ini isinya nama, nomor HP, dan alamat pembeli, jadi cuma boleh dibuka:
        // (a) browser yang bikin pesanan ini (tercatat di session saat checkout), atau
        // (b) pemilik akunnya kalau pesanan ini kesambung ke akun.
        // Selain itu 404 (bukan 403) biar nomor order gak bisa dicek ada/enggaknya.
        $madeInThisSession = in_array($order->order_number, $request->session()->get('owned_orders', []), true);
        $ownedByUser = $order->user_id !== null && Auth::check() && (int) $order->user_id === (int) Auth::id();

        if (! $madeInThisSession && ! $ownedByUser) {
            abort(404);
        }

        $order->load('items');
        return view('pages.order-success', compact('order'));
    }

    // Berat dalam gram, minimal 1 (syarat API ongkir). Dipakai di hitung ongkir & di dalam transaksi
    // biar keduanya pakai aturan pembulatan yang sama.
    private function cartWeight($grams): int
    {
        return max(1, (int) ceil($grams));
    }
}