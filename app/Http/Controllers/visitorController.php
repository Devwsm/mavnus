<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class visitorController extends Controller
{
    //
    public function dashboardIndex(Request $request)
    {
        $today = now()->startOfDay();
        $weekStart = now()->subDays(6)->startOfDay();

        // Ringkasan utama
        $totalVisits = Visit::count();
        $totalUniqueVisitors = (int) Visit::selectRaw('COUNT(DISTINCT ip_address) as total')->value('total');
        $visitsToday = Visit::where('created_at', '>=', $today)->count();
        $uniqueVisitorsToday = (int) Visit::where('created_at', '>=', $today)
            ->selectRaw('COUNT(DISTINCT ip_address) as total')
            ->value('total');
        $visitsThisWeek = Visit::where('created_at', '>=', $weekStart)->count();

        // Grafik kunjungan 7 hari terakhir
        $dailyRaw = Visit::selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->where('created_at', '>=', $weekStart)
            ->groupBy('date')
            ->pluck('total', 'date');

        $dailyStats = collect(range(6, 0))->map(function ($daysAgo) use ($dailyRaw) {
            $date = now()->subDays($daysAgo)->format('Y-m-d');
            return [
                'label' => now()->subDays($daysAgo)->translatedFormat('D'),
                'date'  => $date,
                'total' => (int) $dailyRaw->get($date, 0),
            ];
        })->values();
        $maxDaily = max(1, $dailyStats->max('total'));

        // Halaman paling banyak dikunjungi (hanya top 5, sisanya di halaman /visitors/pages)
        $topPages = Visit::select('url', DB::raw('COUNT(*) as total'))
            ->groupBy('url')
            ->orderByDesc('total')
            ->take(5)
            ->get();
        $maxPageVisit = max(1, optional($topPages->first())->total ?? 1);

        // Breakdown perangkat
        $deviceStats = Visit::select('device_type', DB::raw('COUNT(*) as total'))
            ->groupBy('device_type')
            ->pluck('total', 'device_type');
        $totalDeviceVisits = max(1, $deviceStats->sum());

        // Daftar halaman unik untuk dropdown filter riwayat kunjungan
        $allPageUrls = Visit::select('url')->distinct()->orderBy('url')->pluck('url');
        $selectedUrl = $request->query('page_url');

        // Riwayat kunjungan: opsional difilter per halaman, dibatasi 100 terbaru saja
        $latestQuery = Visit::query()->latest();
        if ($selectedUrl) {
            $latestQuery->where('url', $selectedUrl);
        }
        $latestIds = $latestQuery->take(100)->pluck('id');

        $recentVisits = Visit::whereIn('id', $latestIds)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('pages.dashboard.visitors', compact(
            'totalVisits',
            'totalUniqueVisitors',
            'visitsToday',
            'uniqueVisitorsToday',
            'visitsThisWeek',
            'dailyStats',
            'maxDaily',
            'topPages',
            'maxPageVisit',
            'deviceStats',
            'totalDeviceVisits',
            'allPageUrls',
            'selectedUrl',
            'recentVisits',
        ));
    }

    public function pages()
    {
        $pages = Visit::select('url', DB::raw('COUNT(*) as total'))
            ->groupBy('url')
            ->orderByDesc('total')
            ->paginate(20);
        $maxPageVisit = max(1, optional($pages->first())->total ?? 1);

        return view('pages.dashboard.visitor-pages', compact('pages', 'maxPageVisit'));
    }
}