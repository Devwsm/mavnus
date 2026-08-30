<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\product;
use App\Models\ProductVariant;
use App\Models\Visit;
use Illuminate\Support\Facades\DB;

class dashboardController extends Controller
{
    // Landing dashboard beda isinya tergantung role staff yang login,
    // biar begitu masuk langsung kebaca dia ngapain di sini. Role
    // disimpan di session pas login (lihat loginController).
    public function dashboard()
    {
        return match (session('role')) {
            'admin_produk'  => $this->landingAdminProduk(),
            'staff_pesanan' => $this->landingStaffPesanan(),
            // 'owner' dan fallback role yang gak dikenal -> landing owner,
            // paling aman & paling lengkap ringkasannya.
            default         => $this->landingOwner(),
        };
    }

    // Owner: ringkasan LINTAS SEMUA modul (produk, stok, pesanan, omzet,
    // pengunjung) - owner satu-satunya role yang bisa akses semuanya,
    // jadi landing-nya sengaja dibikin paling lengkap dari 3 role.
    private function landingOwner()
    {
        // Produk aktif dihitung dari kedua kategori, bukan cuma clothes,
        // biar angkanya benar-benar mewakili seluruh katalog.
        $totalProdukAktif = product::whereIn('category', ['clothes', 'accessories'])
            ->where('is_active', true)
            ->count();

        $lowStockCount = ProductVariant::whereHas('product', fn($query) => $query->where('is_active', true))
            ->where('stock', '>', 0)
            ->where('stock', '<=', 3)
            ->count();

        // Stok habis: varian clothes yang stoknya 0 + accessories (stok
        // tunggal, gak punya varian) yang stoknya juga 0.
        $outOfStockVariants = ProductVariant::whereHas('product', fn($query) => $query->where('is_active', true))
            ->where('stock', 0)
            ->count();
        $outOfStockAccessories = product::accessoriesCategory()
            ->where('is_active', true)
            ->where('stock', 0)
            ->count();
        $outOfStockCount = $outOfStockVariants + $outOfStockAccessories;

        // Ringkasan pesanan per status - owner perlu liat seluruh alur,
        // bukan cuma yang pending kayak versi sebelumnya.
        $orderStatusCounts = [
            'pending'    => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'shipped'    => Order::where('status', 'shipped')->count(),
            'completed'  => Order::where('status', 'completed')->count(),
            'cancelled'  => Order::where('status', 'cancelled')->count(),
        ];
        $pesananPendingCount = $orderStatusCounts['pending'];

        // Omzet bulan berjalan, dihitung dari pesanan yang udah completed
        // aja (bukan sekadar dibuat), biar angkanya gak menyesatkan.
        $omzetBulanIni = Order::where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');

        $visitsToday = Visit::where('created_at', '>=', now()->startOfDay())->count();
        $visitsThisWeek = Visit::where('created_at', '>=', now()->subDays(6)->startOfDay())->count();

        // Breakdown perangkat pengunjung - ringkas biar owner gak perlu
        // buka halaman Pengunjung cuma buat liat gambaran cepat.
        $deviceStats = Visit::select('device_type', DB::raw('COUNT(*) as total'))
            ->groupBy('device_type')
            ->pluck('total', 'device_type');
        $totalDeviceVisits = max(1, $deviceStats->sum());

        // Preview 5 pesanan terbaru (apa pun statusnya), buat quick-glance
        $recentOrders = Order::latest()->take(5)->get();

        // Jumlah pesanan per hari, 7 hari terakhir - buat grafik batang kecil
        $ordersTrend = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $ordersTrend->push([
                'label' => $date->translatedFormat('d/m'),
                'count' => Order::whereDate('created_at', $date->toDateString())->count(),
            ]);
        }
        $ordersTrendMax = max(1, $ordersTrend->max('count')); // hindari bagi 0 pas hitung tinggi bar

        // Preview 5 varian clothes paling kritis stoknya (stok 0 duluan,
        // baru yang menipis) - hal yang tadinya cuma keliatan di landing
        // admin produk, sekarang keliatan juga buat owner.
        $criticalVariants = ProductVariant::with('product.images')
            ->whereHas('product', fn($query) => $query->where('is_active', true))
            ->where('stock', '<=', 3)
            ->orderBy('stock')
            ->take(5)
            ->get();

        return view('pages.dashboard.landing-owner', compact(
            'totalProdukAktif',
            'lowStockCount',
            'outOfStockCount',
            'orderStatusCounts',
            'pesananPendingCount',
            'omzetBulanIni',
            'visitsToday',
            'visitsThisWeek',
            'deviceStats',
            'totalDeviceVisits',
            'recentOrders',
            'ordersTrend',
            'ordersTrendMax',
            'criticalVariants',
        ));
    }

    // Admin Produk: fokus stok & katalog - tapi tetep dimaksimalkan seperti
    // landing owner, meliputi SEMUA yang bisa diakses role ini (produk
    // kedua kategori, jadwal rilis, kondisi stok, produk terbaru).
    private function landingAdminProduk()
    {
        $totalProdukAktifClothes = product::clothesCategory()->where('is_active', true)->count();
        $totalProdukAktifAccessories = product::accessoriesCategory()->where('is_active', true)->count();
        $totalProdukAktif = $totalProdukAktifClothes + $totalProdukAktifAccessories;

        // Produk yang udah disimpan tapi belum tayang (nunggu waktu rilis)
        $scheduledCount = product::whereNotNull('published_at')
            ->where('published_at', '>', now())
            ->count();

        $lowStockVariants = ProductVariant::whereHas('product', fn($query) => $query->where('is_active', true))
            ->where('stock', '>', 0)
            ->where('stock', '<=', 3)
            ->count();
        $lowStockAccessories = product::accessoriesCategory()
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->where('stock', '<=', 3)
            ->count();
        $lowStockCount = $lowStockVariants + $lowStockAccessories;

        $outOfStockVariants = ProductVariant::whereHas('product', fn($query) => $query->where('is_active', true))
            ->where('stock', 0)
            ->count();
        $outOfStockAccessories = product::accessoriesCategory()
            ->where('is_active', true)
            ->where('stock', 0)
            ->count();
        $outOfStockCount = $outOfStockVariants + $outOfStockAccessories;

        // Dasar buat breakdown proporsi aman/menipis/habis di grafik batang -
        // varian clothes + produk accessories disatuin sebagai "unit stok"
        // (accessories gak punya varian, jadi 1 produk = 1 unit stok).
        $totalVariants = ProductVariant::whereHas('product', fn($query) => $query->where('is_active', true))->count();
        $totalStockUnits = $totalVariants + $totalProdukAktifAccessories;
        $safeStockCount = max(0, $totalStockUnits - $lowStockCount - $outOfStockCount);

        // Preview 5 item paling kritis stoknya, gabungan varian clothes &
        // produk accessories, diurut dari yang paling sedikit stoknya.
        $criticalClothes = ProductVariant::with('product.images')
            ->whereHas('product', fn($query) => $query->where('is_active', true))
            ->where('stock', '<=', 3)
            ->get()
            ->map(fn($variant) => [
                'product' => $variant->product,
                'label'   => 'Ukuran ' . $variant->label,
                'stock'   => $variant->stock,
            ]);
        $criticalAccessories = product::accessoriesCategory()
            ->with(['images', 'accessories'])
            ->where('is_active', true)
            ->where('stock', '<=', 3)
            ->get()
            ->map(fn($product) => [
                'product' => $product,
                'label'   => optional($product->accessories)->type_label ?? 'Aksesoris',
                'stock'   => $product->stock,
            ]);
        $criticalItems = $criticalClothes->concat($criticalAccessories)
            ->sortBy('stock')
            ->take(5)
            ->values();

        // Preview rilis terjadwal yang paling deket waktunya
        $upcomingScheduled = product::whereNotNull('published_at')
            ->where('published_at', '>', now())
            ->with('images')
            ->orderBy('published_at')
            ->take(5)
            ->get();

        // Preview produk terbaru ditambahkan (apa pun statusnya)
        $recentProducts = product::with('images')
            ->latest()
            ->take(5)
            ->get();

        return view('pages.dashboard.landing-admin-produk', compact(
            'totalProdukAktif',
            'totalProdukAktifClothes',
            'totalProdukAktifAccessories',
            'scheduledCount',
            'lowStockCount',
            'outOfStockCount',
            'totalStockUnits',
            'safeStockCount',
            'criticalItems',
            'upcomingScheduled',
            'recentProducts',
        ));
    }

    // Staff Pesanan: fokus pesanan - dimaksimalkan meliputi SEMUA yang bisa
    // diakses role ini (seluruh status termasuk dibatalkan, tren 7 hari,
    // preview pesanan terbaru, dan pesanan pending yang udah paling lama
    // nunggu diproses).
    private function landingStaffPesanan()
    {
        $orderCounts = [
            'pending'    => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'shipped'    => Order::where('status', 'shipped')->count(),
            'completed'  => Order::where('status', 'completed')->count(),
            'cancelled'  => Order::where('status', 'cancelled')->count(),
        ];

        // Jumlah pesanan per hari, 7 hari terakhir - buat grafik batang kecil
        $ordersTrend = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $ordersTrend->push([
                'label' => $date->translatedFormat('d/m'),
                'count' => Order::whereDate('created_at', $date->toDateString())->count(),
            ]);
        }
        $ordersTrendMax = max(1, $ordersTrend->max('count'));

        // Preview 5 pesanan terbaru (apa pun statusnya), buat quick-glance
        $recentOrders = Order::latest()->take(5)->get();

        // Pesanan pending yang udah paling lama nunggu - biar tau mana yang
        // paling prioritas buat diproses duluan
        $oldestPendingOrder = Order::where('status', 'pending')->oldest()->first();

        return view('pages.dashboard.landing-staff-pesanan', compact(
            'orderCounts',
            'ordersTrend',
            'ordersTrendMax',
            'recentOrders',
            'oldestPendingOrder',
        ));
    }
}