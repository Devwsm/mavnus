<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <title>{{ __('checkout.mail.title') }} - Mavnus</title>
</head>

<body style="margin:0; padding:24px; background:#f4f4f4; font-family:Arial, sans-serif; color:#1a1a1a;">
    <div style="max-width:560px; margin:0 auto; background:#ffffff; border-radius:8px; padding:28px;">
        <div style="font-size:20px; font-weight:bold; color:#B71C1C; letter-spacing:1px;">MAVNUS</div>

        <h1 style="font-size:18px; margin:20px 0 6px;">{{ __('checkout.mail.title') }}</h1>
        <p style="font-size:14px; color:#555; margin:0 0 20px;">
            {{ __('checkout.mail.greeting', ['name' => $order->customer_name]) }}
        </p>

        <p style="font-size:16px; font-weight:bold; margin:0 0 20px;">{{ $order->order_number }}</p>

        <table style="width:100%; border-collapse:collapse; font-size:14px;">
            @foreach ($order->items as $item)
                <tr>
                    <td style="padding:8px 0; border-bottom:1px solid #eee;">
                        {{ $item->product_name }}@if ($item->variant_label)
                            ({{ $item->variant_label }})
                        @endif
                        &times; {{ $item->quantity }}
                    </td>
                    <td style="padding:8px 0; border-bottom:1px solid #eee; text-align:right;">
                        Rp{{ number_format($item->subtotal, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
            <tr>
                <td style="padding:8px 0;">
                    {{ __('checkout.mail.shipping_fee', ['courier' => $order->shipping_courier, 'service' => $order->shipping_service]) }}
                </td>
                <td style="padding:8px 0; text-align:right;">Rp{{ number_format($order->shipping_cost, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td style="padding:8px 0; font-weight:bold; border-top:2px solid #1a1a1a;">{{ __('order.total') }}</td>
                <td style="padding:8px 0; font-weight:bold; border-top:2px solid #1a1a1a; text-align:right;">
                    Rp{{ number_format($order->total, 0, ',', '.') }}
                </td>
            </tr>
        </table>

        <p style="font-size:13px; color:#555; margin:20px 0 4px;"><strong>{{ __('checkout.mail.shipped_to') }}</strong>
        </p>
        <p style="font-size:13px; color:#555; margin:0;">
            {{ $order->customer_name }} &middot; {{ $order->customer_phone }}<br>
            {{ $order->customer_address }}
        </p>

        <p style="font-size:12px; color:#999; margin:28px 0 0;">
            {{ __('checkout.mail.footer') }}
        </p>
    </div>
</body>

</html>
