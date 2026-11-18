<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

// Informasi anggota = pembeli yang membuat akun (tabel users). Pembeli guest tidak termasuk.
class MemberController extends Controller
{
    public function index(Request $request)
    {
        // ?q[]=x (array) bikin error, jadi cuma terima string
        $q = $request->query('q');
        $q = is_string($q) ? trim(mb_substr($q, 0, 100)) : '';

        $members = User::search($q)
            ->withCount('orders')
            ->withSum(['orders as total_spent' => fn($orders) => $orders->where('status', 'completed')], 'total')
            ->latest()
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total'      => User::count(),
            'newMonth'   => User::where('created_at', '>=', now()->startOfMonth())->count(),
            'withOrders' => User::has('orders')->count(),
        ];

        return view('pages.dashboard.members', compact('members', 'stats', 'q'));
    }

    public function show(User $user)
    {
        $orders = $user->orders()->with('items')->latest()->limit(10)->get();

        // Total belanja hanya dari pesanan yang sudah selesai, sama seperti omzet di dashboard
        $totalSpent = (int) $user->orders()->where('status', 'completed')->sum('total');
        $statusCounts = Order::countByStatus($user->id);

        return view('pages.dashboard.member-detail', [
            'member'       => $user,
            'orders'       => $orders,
            'orderCount'   => array_sum($statusCounts),
            'statusCounts' => $statusCounts,
            'totalSpent'   => $totalSpent,
        ]);
    }
}