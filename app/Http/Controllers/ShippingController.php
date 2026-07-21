<?php

namespace App\Http\Controllers;

use App\Services\RajaOngkirService;
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
        $keyword = $request->input('keyword', '');
        if (strlen($keyword) < 3) {
            return response()->json(['data' => []]);
        }

        $results = $this->rajaOngkir->searchDestination($keyword);
        return response()->json(['data' => $results]);
    }

    /**
     * Endpoint AJAX: hitung ongkir berdasarkan tujuan yang dipilih pembeli
     * dan total berat barang di cart.
     */
    public function calculateCost(Request $request)
    {
        $validated = $request->validate([
            'destination_id' => 'required|integer',
            'weight'          => 'required|integer|min:1',
        ]);

        $costs = $this->rajaOngkir->calculateCost(
            $validated['destination_id'],
            $validated['weight']
        );

        return response()->json(['data' => $costs]);
    }
}