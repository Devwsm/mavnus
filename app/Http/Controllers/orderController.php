<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class orderController extends Controller
{
    //
    public function dashboardIndex()
    {
        $orders = Order::with(['items.product.images'])
            ->latest()
            ->paginate(15);
        return view('pages.dashboard.orders', compact('orders'));
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name'    => 'required|string|max:255',
            'customer_phone'   => 'required|string|max:20',
            'customer_address' => 'required|string',
        ], [
            'customer_name.required'    => 'Nama wajib diisi.',
            'customer_phone.required'   => 'Nomor HP wajib diisi.',
            'customer_address.required' => 'Alamat wajib diisi.',
        ]);

        $cartItems = CartItem::where('session_id', session()->getId())
            ->with(['product', 'variant'])
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('home')->with('error', 'Keranjang kamu masih kosong.');
        }

        // Validasi ulang stok sebelum diproses — jaga-jaga stok berubah sejak terakhir cek cart
        foreach ($cartItems as $item) {
            $availableStock = $item->variant ? $item->variant->stock : 99;
            if ($item->quantity > $availableStock) {
                return redirect()->route('cart.index')
                    ->with('error', "Stok {$item->product->name} tidak mencukupi. Sisa stok: {$availableStock}.");
            }
        }

        $order = DB::transaction(function () use ($validated, $cartItems) {
            $subtotal = $cartItems->sum(fn($item) => $item->product->price * $item->quantity);
            // 1. Buat order (header)
            $order = Order::create([
                'order_number'      => Order::generateOrderNumber(),
                'customer_name'     => $validated['customer_name'],
                'customer_phone'    => $validated['customer_phone'],
                'customer_address'  => $validated['customer_address'],
                'subtotal'          => $subtotal,
                'total'             => $subtotal, // ongkir belum dihitung, disusulkan nanti via RajaOngkir
                'status'            => 'pending',
                'payment_status'    => 'unpaid',
            ]);

            // 2. Pindahkan tiap item cart jadi order_items (snapshot data produk)
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id'      => $order->id_order,
                    'product_id'    => $item->product_id,
                    'variant_id'    => $item->variant_id,
                    'product_name'  => $item->product->name,
                    'product_image' => $item->product->images->first()?->image_path,
                    'variant_label' => $item->variant->label ?? null,
                    'weight'        => $item->product->weight,
                    'price'         => $item->product->price,
                    'quantity'      => $item->quantity,
                    'subtotal'      => $item->product->price * $item->quantity,
                ]);

                // 3. Kurangi stok variant yang dipesan
                if ($item->variant) {
                    $item->variant->decrement('stock', $item->quantity);
                    $item->variant->product->syncActiveStatus();
                }
            }

            // 4. Bersihkan cart setelah semua dipindahkan ke order
            CartItem::where('session_id', session()->getId())->delete();
            return $order;
        });

        return redirect()->route('order.success', $order)->with('success', 'Pesanan berhasil dibuat.');
    }

    public function success(Order $order)
    {
        $order->load('items');
        return view('pages.order-success', compact('order'));
    }
}