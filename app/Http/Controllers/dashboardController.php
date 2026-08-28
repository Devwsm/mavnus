<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\product;
use App\Models\ProductVariant;
use App\Models\Visit;

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

    // Owner: ringkasan lintas-modul + preview pesanan terbaru + tren 7 hari.
    private function landingOwner()
    {
        $totalProdukAktif = product::clothesCategory()->where('is_active', true)->count();

        $lowStockCount = ProductVariant::whereHas('product', fn($query) => $query->where('is_active', true))
            ->where('stock', '>', 0)
            ->where('stock', '<=', 3)
            ->count();

        $pesananPendingCount = Order::where('status', 'pending')->count();

        $visitsToday = Visit::where('created_at', '>=', now()->startOfDay())->count();

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

        return view('pages.dashboard.landing-owner', compact(
            'totalProdukAktif',
            'lowStockCount',
            'pesananPendingCount',
            'visitsToday',
            'recentOrders',
            'ordersTrend',
            'ordersTrendMax',
        ));
    }

    // Admin Produk: fokus stok & katalog + preview produk kritis + breakdown stok.
    private function landingAdminProduk()
    {
        $totalProdukAktif = product::clothesCategory()->where('is_active', true)->count();

        $lowStockCount = ProductVariant::whereHas('product', fn($query) => $query->where('is_active', true))
            ->where('stock', '>', 0)
            ->where('stock', '<=', 3)
            ->count();

        $outOfStockCount = ProductVariant::whereHas('product', fn($query) => $query->where('is_active', true))
            ->where('stock', 0)
            ->count();

        // Total varian dari produk yang masih aktif - dasar buat breakdown
        // proporsi aman/menipis/habis di grafik batang
        $totalVariants = ProductVariant::whereHas('product', fn($query) => $query->where('is_active', true))->count();
        $safeVariantCount = max(0, $totalVariants - $lowStockCount - $outOfStockCount);

        // Preview 5 varian paling kritis (stok 0 duluan, baru yang menipis)
        $criticalVariants = ProductVariant::with('product.images')
            ->whereHas('product', fn($query) => $query->where('is_active', true))
            ->where('stock', '<=', 3)
            ->orderBy('stock')
            ->take(5)
            ->get();

        return view('pages.dashboard.landing-admin-produk', compact(
            'totalProdukAktif',
            'lowStockCount',
            'outOfStockCount',
            'totalVariants',
            'safeVariantCount',
            'criticalVariants',
        ));
    }

    // Staff Pesanan: fokus pesanan per status, gak ada data produk/stok.
    private function landingStaffPesanan()
    {
        $orderCounts = [
            'pending'    => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'shipped'    => Order::where('status', 'shipped')->count(),
            'completed'  => Order::where('status', 'completed')->count(),
        ];

        return view('pages.dashboard.landing-staff-pesanan', compact('orderCounts'));
    }
}