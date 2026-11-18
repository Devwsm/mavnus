<?php

namespace App\Http\Controllers;

use App\Models\product;
use App\Support\Like;
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

        // % dan _ dicari apa adanya, bukan dianggap wildcard LIKE (tanpa ini "%" cocok ke semua produk)
        $products = product::whereRaw(Like::sql('name'), [Like::contains($query)])
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
                    'category' => __('site.category.' . $product->category),
                    'image'    => $product->images->first()
                        ? asset('storage/' . $product->images->first()->image_path)
                        : null,
                    'url'      => route($routeName, $product->slug),
                ];
            });

        return response()->json(['results' => $products]);
    }
}