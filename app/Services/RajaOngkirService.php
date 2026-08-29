<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RajaOngkirService
{
    protected string $apiKey;
    protected string $baseUrl;
    protected string $originId;

    public function __construct()
    {
        $this->apiKey   = config('services.rajaongkir.api_key');
        $this->baseUrl  = config('services.rajaongkir.base_url');
        $this->originId = config('services.rajaongkir.origin_id');
    }

    /**
     * Cari kota/kecamatan tujuan berdasarkan keyword (dipakai untuk live search
     * alamat pembeli di form checkout).
     */
    public function searchDestination(string $keyword): array
    {
        $response = Http::withoutVerifying()->withHeaders([
            'key' => $this->apiKey,
        ])->get("{$this->baseUrl}/destination/domestic-destination", [
            'search' => $keyword,
        ]);

        if (!$response->successful()) {
            return [];
        }
        return $response->json('data', []);
    }

    /**
     * Hitung ongkir dari toko Mavnus (origin tetap) ke tujuan pembeli.
     *
     * @param int $destinationId ID kecamatan tujuan (dari searchDestination)
     * @param int $weight Berat total dalam gram
     * @param array $couriers Daftar kode kurir, misal ['jne', 'jnt', 'sicepat']
     */
    public function calculateCost(int $destinationId, int $weight, array $couriers = ['jne', 'jnt', 'sicepat']): array
    {
        $response = Http::withoutVerifying()->asForm()->withHeaders([
            'key' => $this->apiKey,
        ])->post("{$this->baseUrl}/calculate/domestic-cost", [
            'origin'      => $this->originId,
            'destination' => $destinationId,
            'weight'      => $weight,
            'courier'     => implode(':', $couriers),
            'price'       => 'lowest',
        ]);

        if (!$response->successful()) {
            return [];
        }

        $data = $response->json('data', []);
        // Buang layanan trucking/cargo — gak relevan buat paket kecil (merch/baju)
        $excludedServices = ['JTR', 'JTR<130', 'JTR>130', 'JTR>200', 'GOKIL'];
        
        return collect($data)
            ->reject(fn($item) => in_array($item['service'], $excludedServices))
            ->values()
            ->all();
    }
}