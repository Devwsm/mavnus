<?php

namespace App\Http\Controllers;

use App\Models\product;
use Illuminate\Http\Request;

class searchController extends Controller
{
    //
    public function search(Request $request)
    {
        // ?q[]=x (array) bikin strlen() error 500, jadi cuma terima string
        $query = $request->input('q', '');
        $query = is_string($query) ? trim(mb_substr($query, 0, 100)) : '';

        if (mb_strlen($query) < 2) {
            return response()->json(['results' => []]);
        }

        // Escape \ % _ biar karakter itu dicari apa adanya, bukan dianggap wildcard LIKE
        // (tanpa ini "%" atau "_" cocok ke semua produk)
        $escaped = addcslashes($query, '\\%_');

        $products = product::where('name', 'like', "%{$escaped}%")
            ->active()
            ->with('images')
            ->limit(8)
            ->get()
            ->map(function ($product) {
                $routeName = match ($product->category) {
                    'clothes'     => 'product_detail.clothes',
                    'accessories' => 'product_detail.accessories',
                };

                return [
                    'name'     => $product->name,
                    'price'    => $product->formatted_price,
                    'category' => ucfirst($product->category),
                    'image'    => $product->images->first()
                        ? asset('storage/' . $product->images->first()->image_path)
                        : null,
                    'url'      => route($routeName, $product->slug),
                ];
            });

        return response()->json(['results' => $products]);
    }
}