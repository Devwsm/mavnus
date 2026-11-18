{{-- Badge status pesanan. Warna & ikon dari Order::STATUS_META (satu sumber untuk seluruh dashboard). --}}
@props(['order', 'icon' => false])
@php $meta = \App\Models\Order::STATUS_META[$order->status] ?? ['color' => '#999999', 'icon' => null]; @endphp
<span
    {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 text-[10px] font-semibold uppercase tracking-wide px-2.5 py-1 rounded-full']) }}
    style="color: {{ $meta['color'] }}; background-color: {{ $meta['color'] }}1a">
    @if ($icon && $meta['icon'])
        <i class="bi {{ $meta['icon'] }}"></i>
    @endif
    {{ $order->statusLabel() }}
</span>
