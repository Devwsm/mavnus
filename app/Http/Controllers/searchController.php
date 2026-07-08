<?php

namespace App\Http\Controllers;

use App\Models\product;
use Illuminate\Http\Request;

class searchController extends Controller
{
    //
    public function search(Request $request)
    {
        $query = $request->input('q', '');

        if (strlen($query) < 2) {
            return response()->json(['results' => []]);
        }

        $products = product::where('name', 'like', "%{$query}%")
            ->active()
            ->with('images')
            ->limit(8)
            ->get()
            ->map(function ($product) {
                $routeName = match ($product->category) {
                    'clothes'     => 'product_detail.clothes',
                    'accessories' => 'product_detail.accessories',
                    'album'       => 'product_detail.albums',
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