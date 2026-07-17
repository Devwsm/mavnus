<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice Pesanan - Mavnus</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #1a1a1a;
        }

        .invoice {
            max-width: 700px;
            margin: 0 auto 40px;
            page-break-after: always;
        }

        .invoice:last-child {
            page-break-after: avoid;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 3px solid #B71C1C;
            padding-bottom: 16px;
            margin-bottom: 20px;
        }

        .brand {
            font-size: 22px;
            font-weight: bold;
            color: #B71C1C;
            letter-spacing: 1px;
        }

        .invoice-meta {
            text-align: right;
            font-size: 13px;
            color: #555;
        }

        .invoice-meta .order-number {
            font-size: 16px;
            font-weight: bold;
            color: #1a1a1a;
        }

        .section-title {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #888;
            margin-bottom: 6px;
        }

        .bill-to {
            margin-bottom: 24px;
            font-size: 13px;
            line-height: 1.6;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table.items th {
            background: #1a1a1a;
            color: #fff;
            text-align: left;
            padding: 8px 10px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        table.items td {
            padding: 8px 10px;
            font-size: 13px;
            border-bottom: 1px solid #eee;
        }

        table.items .align-right {
            text-align: right;
        }

        .totals {
            width: 260px;
            margin-left: auto;
            font-size: 13px;
        }

        .totals td {
            padding: 4px 0;
        }

        .totals .grand-total td {
            border-top: 2px solid #1a1a1a;
            font-weight: bold;
            font-size: 15px;
            padding-top: 8px;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .footer-note {
            text-align: center;
            font-size: 11px;
            color: #999;
            margin-top: 24px;
        }
    </style>
</head>

<body>

    @foreach ($orders as $order)
        <div class="invoice">
            <div class="header">
                <div>
                    <div class="brand">MAVNUS</div>
                    <div style="font-size: 12px; color: #888; margin-top: 4px;">Official Merchandise Store</div>
                </div>
                <div class="invoice-meta">
                    <div class="order-number">{{ $order->order_number }}</div>
                    <div>{{ $order->created_at->format('d F Y, H:i') }} WIB</div>
                    <div style="margin-top: 6px;">
                        <span class="status-badge"
                            style="background: {{ $order->payment_status === 'paid' ? '#1C7B1C' : '#B77B1C' }}; color: #fff;">
                            {{ $order->payment_status === 'paid' ? 'Lunas' : 'Belum Dibayar' }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="bill-to">
                <div class="section-title">Ditagihkan Kepada</div>
                <strong>{{ $order->customer_name }}</strong><br>
                {{ $order->customer_phone }}<br>
                {{ $order->customer_address }}
            </div>

            <table class="items">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Ukuran</th>
                        <th class="align-right">Qty</th>
                        <th class="align-right">Harga</th>
                        <th class="align-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                            <td>{{ $item->product_name }}</td>
                            <td>{{ $item->variant_label ?? '-' }}</td>
                            <td class="align-right">{{ $item->quantity }}</td>
                            <td class="align-right">Rp{{ number_format($item->price, 0, ',', '.') }}</td>
                            <td class="align-right">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <table class="totals">
                <tr>
                    <td>Subtotal</td>
                    <td class="align-right">Rp{{ number_format($order->subtotal, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Ongkos Kirim</td>
                    <td class="align-right">
                        {{ $order->shipping_cost > 0 ? 'Rp' . number_format($order->shipping_cost, 0, ',', '.') : '-' }}
                    </td>
                </tr>
                <tr class="grand-total">
                    <td>Total</td>
                    <td class="align-right">Rp{{ number_format($order->total, 0, ',', '.') }}</td>
                </tr>
            </table>

            <div class="footer-note">Terima kasih telah berbelanja di Mavnus.</div>
        </div>
    @endforeach

</body>

</html>
