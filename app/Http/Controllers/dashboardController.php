<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\product;
use Illuminate\Http\Request;

class dashboardController extends Controller
{
    //
    public function dashboard()
    {
        $products = product::clothesCategory()
            ->with(['images', 'clothes', 'variants'])
            ->latest()
            ->get();
        $recentOrders = Order::with('items')
            ->latest()
            ->take(5)
            ->get();
        return view('pages.dashboard', compact('products', 'recentOrders'));
    }
}