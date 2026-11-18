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
    // Satu ringkasan untuk semua staf: produk, stok, pesanan, omzet, pengunjung, dan anggota.
    public function dashboard()
    {
        $stock = Stock::summary();
        $ordersTrend = Stats::dailyCounts(Order::query(), 7);
        $omzetPerBulan = $this->monthlyRevenue(6);
        $omzetPerKategori = $this->revenueByCategory();
        $deviceStats = Visit::deviceBreakdown();

        return view('pages.dashboard.index', [
            // Kartu ringkasan
            'totalProdukAktif'      => product::whereIn('category', ['clothes', 'accessories'])->where('is_active', true)->count(),
            'scheduledCount'        => product::where('published_at', '>', now())->count(),
            'lowStockCount'         => $stock['low'],
            'outOfStockCount'       => $stock['out'],
            'omzetBulanIni'         => $omzetPerBulan->last()['total'],
            'visitsToday'           => Visit::where('created_at', '>=', now()->startOfDay())->count(),
            'visitsThisWeek'        => Visit::where('created_at', '>=', now()->subDays(6)->startOfDay())->count(),
            'totalMembers'          => User::count(),
            'newMembersThisMonth'   => User::where('created_at', '>=', now()->startOfMonth())->count(),

            // Pesanan
            'orderStatusCounts'     => Order::countByStatus(),
            'recentOrders'          => Order::latest()->take(5)->get(),
            'ordersTrend'           => $ordersTrend,
            'ordersTrendMax'        => Stats::max($ordersTrend),

            // Stok & produk
            'criticalItems'         => Stock::critical(5),
            'topProduk'             => $this->topProducts(5),

            // Omzet
            'omzetPerBulan'         => $omzetPerBulan,
            'omzetPerBulanMax'      => Stats::max($omzetPerBulan),
            'omzetPerKategori'      => $omzetPerKategori,
            'omzetPerKategoriTotal' => max(1, array_sum($omzetPerKategori)),

            // Pengunjung & anggota
            'deviceStats'           => $deviceStats,
            'totalDeviceVisits'     => max(1, $deviceStats->sum()),
            'recentMembers'         => User::withCount('orders')->latest()->orderByDesc('id')->take(5)->get(),
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
     * Omzet bulan ini per kategori (clothes vs accessories). Item yang produknya sudah dihapus
     * (product_id null) tidak ikut breakdown ini, tapi tetap terhitung di omzet bulan ini.
     *
     * @return array{clothes: int, accessories: int}
     */
    private function revenueByCategory(): array
    {
        $raw = OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id_order')
            ->join('products', 'order_items.product_id', '=', 'products.id_product')
            ->where('orders.status', 'completed')
            ->where('orders.created_at', '>=', now()->startOfMonth())
            ->select('products.category', DB::raw('SUM(order_items.subtotal) as total'))
            ->groupBy('products.category')
            ->pluck('total', 'category');

        return [
            'clothes'     => (int) ($raw['clothes'] ?? 0),
            'accessories' => (int) ($raw['accessories'] ?? 0),
        ];
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