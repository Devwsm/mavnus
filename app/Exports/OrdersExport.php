<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrdersExport implements FromCollection, WithHeadings, WithMapping
{
    private ?Collection $orders = null;

    /** @param array $filters q, status, from, to (lihat Order::scopeFilter) */
    public function __construct(private array $filters = []) {}

    public function collection()
    {
        // Disimpan supaya pengecekan "hasil kosong" di controller tidak query dua kali
        return $this->orders ??= Order::with('items')->filter($this->filters)->latest()->get();
    }

    public function headings(): array
    {
        return ['No. Order', 'Nama Pembeli', 'No. HP', 'Email', 'Alamat', 'Status', 'Status Bayar', 'Subtotal', 'Ongkir', 'Total', 'Tanggal', 'Rincian Barang'];
    }

    public function map($order): array
    {
        $itemsDetail = $order->items
            ->map(fn($item) => "{$item->product_name}" . ($item->variant_label ? " ({$item->variant_label})" : '') . " x{$item->quantity}")
            ->implode(', ');
        return [
            $order->order_number,
            $order->customer_name,
            $order->customer_phone,
            $order->customer_email,
            $order->customer_address,
            $order->statusLabel(),
            ucfirst($order->payment_status),
            $order->subtotal,
            $order->shipping_cost,
            $order->total,
            $order->created_at->format('d-m-Y H:i'),
            $itemsDetail,
        ];
    }
}