<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\product;
use App\Models\ProductVariant;
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

        // Varian dengan stok menipis (1-3 pcs, belum sampai habis) dari produk yang masih aktif
        $lowStockVariants = ProductVariant::with('product.images')
            ->whereHas('product', fn($query) => $query->where('is_active', true))
            ->where('stock', '>', 0)
            ->where('stock', '<=', 3)
            ->orderBy('stock')
            ->get();

        return view('pages.dashboard', compact('products', 'recentOrders', 'lowStockVariants'));
    }
}