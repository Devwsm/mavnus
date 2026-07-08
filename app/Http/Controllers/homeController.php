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

        $accessoriesProducts = product::accessoriesCategory()
            ->active()
            ->with('images')
            ->latest()
            ->take(4)
            ->get();

        $albumsProducts = product::albumsCategory()
            ->active()
            ->with('images')
            ->latest()
            ->take(4)
            ->get();

        return view('pages.home', compact('clothesProducts', 'accessoriesProducts', 'albumsProducts'));
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
        $products = product::accessoriesCategory()
            ->active()
            ->with('images')
            ->latest()
            ->paginate(12);
        return view('pages.accessoris', compact('products'));
    }
    
    public function albums()
    {
        $products = product::albumsCategory()
            ->active()
            ->with('images')
            ->latest()
            ->paginate(12);
        return view('pages.albums', compact('products'));
    }
}