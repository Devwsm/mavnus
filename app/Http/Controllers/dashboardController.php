<?php

namespace App\Http\Controllers;

use App\Models\product;
use Illuminate\Http\Request;

class dashboardController extends Controller
{
    //
    public function dashboard()
    {
        $products = product::clothesCategory()
            ->with(['images', 'clothes.variants'])
            ->latest()
            ->get();
        return view('pages.dashboard', compact('products'));
    }
}