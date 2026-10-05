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

        // Produk yang masih dijadwalkan gak boleh masuk cart walau product_id-nya ketebak
        $product = \App\Models\product::find($validated['product_id']);
        if ($product->is_scheduled) {
            return response()->json([
                'message' => 'Produk ini belum resmi rilis.',
            ], 422);
        }

        // Varian harus nyambung ke produknya: clothes wajib pilih varian (ukuran) milik
        // produk itu sendiri, accessories gak punya varian sama sekali. Tanpa cek ini,
        // variant_id produk lain bisa nyelip dan stok/harga jadi gak sinkron.
        $variantId = $validated['variant_id'] ?? null;
        $variant = null;

        if ($product->category === 'clothes') {
            $variant = $variantId ? ProductVariant::find($variantId) : null;
            if (! $variant || $variant->product_id !== $product->id_product) {
                return response()->json(['message' => 'Pilih ukuran yang tersedia untuk produk ini.'], 422);
            }
        } elseif ($variantId) {
            return response()->json(['message' => 'Produk ini tidak punya pilihan ukuran.'], 422);
        }

        // accessories gak punya varian, stoknya langsung dari produk
        $maxStock = $variant ? $variant->stock : (int) $product->stock;

        if ($maxStock < 1) {
            return response()->json(['message' => 'Stok produk ini habis.'], 422);
        }

        $existing = CartItem::where('session_id', session()->getId())
            ->where('product_id', $validated['product_id'])
            ->where('variant_id', $validated['variant_id'] ?? null)
            ->first();

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
        $this->ensureOwnedBySession($cartItem);

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);
        $maxStock = $cartItem->variant_id
            ? $cartItem->variant->stock
            : $cartItem->product->stock; // accessories gak punya varian, stoknya langsung dari produk
        $cartItem->update([
            'quantity' => min($validated['quantity'], $maxStock),
        ]);
        return $this->index();
    }

    public function destroy(CartItem $cartItem)
    {
        $this->ensureOwnedBySession($cartItem);

        $cartItem->delete();
        return $this->index();
    }

    // Item cart cuma boleh diubah/dihapus oleh session yang punya. Tanpa ini, siapa pun
    // bisa nebak id_cart_item (angka urut) lalu ngubah/hapus keranjang orang lain.
    private function ensureOwnedBySession(CartItem $cartItem): void
    {
        abort_if($cartItem->session_id !== session()->getId(), 404);
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
            'max'      => $item->variant_id ? $item->variant->stock : $item->product->stock,
            'image'    => $item->product->images->first()
                ? asset('storage/' . $item->product->images->first()->image_path)
                : null,
        ];
    }
}