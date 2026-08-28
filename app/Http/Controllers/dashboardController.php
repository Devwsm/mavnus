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

    // Owner: ringkasan lintas-modul + quick link ke semua halaman.
    private function landingOwner()
    {
        $totalProdukAktif = product::clothesCategory()->where('is_active', true)->count();

        $lowStockCount = ProductVariant::whereHas('product', fn($query) => $query->where('is_active', true))
            ->where('stock', '>', 0)
            ->where('stock', '<=', 3)
            ->count();

        $pesananPendingCount = Order::where('status', 'pending')->count();

        $visitsToday = Visit::where('created_at', '>=', now()->startOfDay())->count();

        return view('pages.dashboard.landing-owner', compact(
            'totalProdukAktif',
            'lowStockCount',
            'pesananPendingCount',
            'visitsToday',
        ));
    }

    // Admin Produk: fokus stok & katalog, gak ada data pesanan/omzet.
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

        return view('pages.dashboard.landing-admin-produk', compact(
            'totalProdukAktif',
            'lowStockCount',
            'outOfStockCount',
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