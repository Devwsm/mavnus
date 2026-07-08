<?php

namespace App\Http\Controllers;

use App\Models\product;
use Illuminate\Http\Request;

class homeController extends Controller
{
    //
    public function home()
    {
        $clothesProducts = product::clothesCategory()
            ->active()               // hanya produk yang stoknya masih ada (bukan Sold Out)
            ->with(['images', 'clothes.variants']) // sekalian ambil foto & detail warna/material/stok, hindari N+1 query
            ->latest()                // urutkan dari produk terbaru ditambahkan
            ->take(4)                 // batasi cuma 4 produk (sesuai slot grid di landing page)
            ->get();                  // eksekusi query, hasilnya disimpan ke variable
        return view('pages.home', compact('clothesProducts'));
    }
    public function clothes()
    {
        $products = product::clothesCategory()
            ->active()
            ->with(['images', 'clothes.variants'])
            ->latest()
            ->paginate(12);
        return view('pages.clothes', compact('products'));
    }
    public function accessoris()
    {
        return view('pages.accessoris');
    }
    public function albums()
    {
        return view('pages.albums');
    }
}