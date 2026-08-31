<?php

namespace App\Services;

use App\Exceptions\RajaOngkirException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
     *
     * @throws RajaOngkirException kalau gagal konek atau API-nya lagi bermasalah
     */
    public function searchDestination(string $keyword): array
    {
        try {
            $response = Http::withoutVerifying()->timeout(10)->withHeaders([
                'key' => $this->apiKey,
            ])->get("{$this->baseUrl}/destination/domestic-destination", [
                'search' => $keyword,
            ]);
        } catch (ConnectionException $e) {
            Log::warning('RajaOngkir searchDestination gagal konek', ['error' => $e->getMessage()]);
            throw new RajaOngkirException('Gagal terhubung ke layanan pencarian alamat. Coba lagi dalam beberapa saat.', previous: $e);
        }

        if (!$response->successful()) {
            Log::warning('RajaOngkir searchDestination gagal', ['status' => $response->status()]);
            throw new RajaOngkirException('Layanan pencarian alamat sedang bermasalah. Coba lagi dalam beberapa saat.');
        }

        return $response->json('data', []);
    }

    /**
     * Hitung ongkir dari toko Mavnus (origin tetap) ke tujuan pembeli.
     *
     * @param int $destinationId ID kecamatan tujuan (dari searchDestination)
     * @param int $weight Berat total dalam gram
     * @param array $couriers Daftar kode kurir, misal ['jne', 'jnt', 'sicepat']
     * @throws RajaOngkirException kalau gagal konek atau API-nya lagi bermasalah
     */
    public function calculateCost(int $destinationId, int $weight, array $couriers = ['jne', 'jnt', 'sicepat']): array
    {
        try {
            $response = Http::withoutVerifying()->timeout(10)->asForm()->withHeaders([
                'key' => $this->apiKey,
            ])->post("{$this->baseUrl}/calculate/domestic-cost", [
                'origin'      => $this->originId,
                'destination' => $destinationId,
                'weight'      => $weight,
                'courier'     => implode(':', $couriers),
                'price'       => 'lowest',
            ]);
        } catch (ConnectionException $e) {
            Log::warning('RajaOngkir calculateCost gagal konek', ['error' => $e->getMessage()]);
            throw new RajaOngkirException('Gagal terhubung ke layanan pengiriman. Coba lagi dalam beberapa saat.', previous: $e);
        }

        if (!$response->successful()) {
            Log::warning('RajaOngkir calculateCost gagal', ['status' => $response->status()]);
            throw new RajaOngkirException('Gagal menghitung ongkos kirim. Coba lagi dalam beberapa saat.');
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