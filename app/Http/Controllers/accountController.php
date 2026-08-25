<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class accountController extends Controller
{
    // Halaman akun customer - cuma bisa diakses kalau udah login (middleware 'auth' bawaan Laravel)
    public function index()
    {
        return view('pages.account', [
            'user' => Auth::user(),
        ]);
    }

    // Riwayat pesanan - cuma nampilin order milik akun yang lagi login
    public function orders()
    {
        $orders = Order::where('user_id', Auth::id())
            ->with('items')
            ->latest()
            ->paginate(10);

        return view('pages.account-orders', [
            'user'   => Auth::user(),
            'orders' => $orders,
        ]);
    }
}