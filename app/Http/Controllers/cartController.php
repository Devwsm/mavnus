<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class cartController extends Controller
{
    public function index()
    {
        $items = CartItem::where('session_id', session()->getId())
            ->with(['product.images', 'variant'])
            ->get();
        return response()->json([
            'items' => $items->map(fn($item) => $this->formatItem($item)),
            'total' => $items->sum(fn($item) => $item->product->price * $item->quantity),
            'count' => $items->sum('quantity'),
        ]);
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id_product',
            'variant_id' => 'nullable|exists:product_variants,id_variant',
            'quantity'   => 'required|integer|min:1',
        ]);

        $existing = CartItem::where('session_id', session()->getId())
            ->where('product_id', $validated['product_id'])
            ->where('variant_id', $validated['variant_id'] ?? null)
            ->first();

        $maxStock = isset($validated['variant_id'])
            ? ProductVariant::find($validated['variant_id'])->stock
            : 99;

        if ($existing) {
            $existing->quantity = min($existing->quantity + $validated['quantity'], $maxStock);
            $existing->save();
        } else {
            CartItem::create([
                'session_id' => session()->getId(),
                'product_id' => $validated['product_id'],
                'variant_id' => $validated['variant_id'] ?? null,
                'quantity'   => min($validated['quantity'], $maxStock),
            ]);
        }

        return $this->index();
    }

    public function update(Request $request, CartItem $cartItem)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);
        $maxStock = $cartItem->variant_id
            ? $cartItem->variant->stock
            : 99;
        $cartItem->update([
            'quantity' => min($validated['quantity'], $maxStock),
        ]);
        return $this->index();
    }

    public function destroy(CartItem $cartItem)
    {
        $cartItem->delete();
        return $this->index();
    }

    private function formatItem(CartItem $item): array
    {
        return [
            'id'       => $item->id_cart_item,
            'name'     => $item->product->name,
            'size'     => $item->variant->label ?? null,
            'price'    => $item->product->formatted_price,
            'subtotal' => 'Rp' . number_format($item->product->price * $item->quantity, 0, ',', '.'),
            'quantity' => $item->quantity,
            'max'      => $item->variant_id ? $item->variant->stock : 99,
            'image'    => $item->product->images->first()
                ? asset('storage/' . $item->product->images->first()->image_path)
                : null,
        ];
    }
}