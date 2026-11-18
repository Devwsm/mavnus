<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\product;
use App\Models\User;
use App\Models\Visit;
use App\Support\Stats;
use App\Support\Stock;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class dashboardController extends Controller
{
    // Satu ringkasan untuk semua staf, sengaja singkat: apa yang perlu ditindak, angka utama,
    // pesanan terbaru, dan (kalau sudah ada penjualan) tren omzet + produk terlaris.
    // Statistik pengunjung punya halaman sendiri, jadi tidak dicampur di sini.
    public function dashboard()
    {
        $stock = Stock::summary();
        $orderStatusCounts = Order::countByStatus();
        $omzetPerBulan = $this->monthlyRevenue(6);

        return view('pages.dashboard.index', [
            // Perlu ditindak
            'pendingOrders'       => $orderStatusCounts['pending'],
            'lowStockCount'       => $stock['low'],
            'outOfStockCount'     => $stock['out'],

            // Angka utama
            'omzetBulanIni'       => $omzetPerBulan->last()['total'],
            'totalOrders'         => array_sum($orderStatusCounts),
            'totalProdukAktif'    => product::whereIn('category', ['clothes', 'accessories'])->where('is_active', true)->count(),
            'scheduledCount'      => product::where('published_at', '>', now())->count(),
            'totalMembers'        => User::count(),
            'newMembersThisMonth' => User::where('created_at', '>=', now()->startOfMonth())->count(),

            // Daftar
            'recentOrders'        => Order::latest()->take(5)->get(),
            'criticalItems'       => Stock::critical(5),

            // Penjualan (hanya tampil kalau sudah ada pesanan selesai)
            'omzetPerBulan'       => $omzetPerBulan,
            'omzetPerBulanMax'    => Stats::max($omzetPerBulan),
            'hasSales'            => $omzetPerBulan->sum('total') > 0,
            'topProduk'           => $this->topProducts(5),

            'visitsToday'         => Visit::where('created_at', '>=', now()->startOfDay())->count(),
        ]);
    }

    /**
     * Omzet per bulan untuk N bulan terakhir (bulan berjalan ikut), dari pesanan completed saja
     * supaya angkanya tidak menyesatkan. Satu query, dikelompokkan per bulan di PHP biar jalan
     * di MySQL maupun SQLite.
     *
     * @return Collection<int, array{label: string, total: int}>
     */
    private function monthlyRevenue(int $months): Collection
    {
        $firstMonth = now()->startOfMonth()->subMonths($months - 1);

        $totals = Order::where('status', 'completed')
            ->where('created_at', '>=', $firstMonth)
            ->get(['created_at', 'total'])
            ->groupBy(fn(Order $order) => $order->created_at->format('Y-m'))
            ->map(fn(Collection $orders) => (int) $orders->sum('total'));

        return collect(range($months - 1, 0))->map(function (int $monthsAgo) use ($totals) {
            $month = now()->startOfMonth()->subMonths($monthsAgo);

            return [
                'label' => $month->translatedFormat('M Y'),
                'total' => $totals->get($month->format('Y-m'), 0),
            ];
        })->values();
    }

    /**
     * Produk terlaris sepanjang waktu, diurut dari omzet terbesar. Memakai snapshot nama/foto
     * di order_items (bukan join ke products) supaya tetap terbaca walau produk sudah diedit/dihapus.
     */
    private function topProducts(int $limit): Collection
    {
        return OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id_order')
            ->where('orders.status', 'completed')
            ->select(
                'order_items.product_name',
                'order_items.product_image',
                DB::raw('SUM(order_items.quantity) as total_qty'),
                DB::raw('SUM(order_items.subtotal) as total_omzet')
            )
            ->groupBy('order_items.product_name', 'order_items.product_image')
            ->orderByDesc('total_omzet')
            ->take($limit)
            ->get();
    }
}