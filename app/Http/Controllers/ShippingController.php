<?php

namespace App\Http\Controllers;

use App\Exceptions\RajaOngkirException;
use App\Models\CartItem;
use App\Services\RajaOngkirService;
use App\Support\CartSession;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    //
    public function __construct(protected RajaOngkirService $rajaOngkir) {}

    /**
     * Endpoint AJAX: cari kota/kecamatan tujuan, dipanggil dari form checkout
     * saat pembeli mengetik alamat mereka.
     */
    public function searchDestination(Request $request)
    {
        // ?keyword[]=x (array) bikin strlen() error 500, jadi cuma terima string
        $keyword = $request->input('keyword', '');
        $keyword = is_string($keyword) ? trim(mb_substr($keyword, 0, 100)) : '';
        if (mb_strlen($keyword) < 3) {
            return response()->json(['data' => []]);
        }

        try {
            $results = $this->rajaOngkir->searchDestination($keyword);
        } catch (RajaOngkirException $e) {
            return response()->json(['data' => [], 'error' => $e->getMessage()], 503);
        }

        return response()->json(['data' => $results]);
    }

    /**
     * Endpoint AJAX: hitung ongkir berdasarkan tujuan yang dipilih pembeli
     * dan total berat barang di cart. Berat dihitung dari cart di server,
     * bukan dari input browser.
     */
    public function calculateCost(Request $request)
    {
        $validated = $request->validate([
            'destination_id' => 'required|integer|min:1',
        ]);

        $grams = CartItem::where('session_id', CartSession::key())
            ->with('product')
            ->get()
            ->sum(fn($item) => $item->product->weight * $item->quantity);

        if ($grams <= 0) {
            return response()->json(['data' => [], 'error' => 'Keranjang kamu masih kosong.'], 422);
        }

        try {
            $costs = $this->rajaOngkir->calculateCost(
                (int) $validated['destination_id'],
                max(1, (int) ceil($grams))
            );
        } catch (RajaOngkirException $e) {
            return response()->json(['data' => [], 'error' => $e->getMessage()], 503);
        }

        return response()->json(['data' => $costs]);
    }
}