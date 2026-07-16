<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrdersExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Order::with('items')->latest()->get();
    }

    public function headings(): array
    {
        return ['No. Order', 'Nama Pembeli', 'No. HP', 'Alamat', 'Status', 'Status Bayar', 'Subtotal', 'Ongkir', 'Total', 'Tanggal', 'Rincian Barang'];
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
            $order->customer_address,
            ucfirst($order->status),
            ucfirst($order->payment_status),
            $order->subtotal,
            $order->shipping_cost,
            $order->total,
            $order->created_at->format('d-m-Y H:i'),
            $itemsDetail,
        ];
    }
}