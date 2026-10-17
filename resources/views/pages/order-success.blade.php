@extends('template.bare-layout')
@section('title', __('checkout.success.title'))
@section('content')
    <section id="main-content" class="flex flex-col w-full bg-white gap-10 p-6 lg:p-14 py-14 min-h-screen justify-center">
        <div class="flex flex-col items-center text-center gap-3">
            <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center">
                <i class="bi bi-check-lg text-green-600 text-3xl"></i>
            </div>
            <h1 class="text-2xl md:text-3xl font-bold uppercase tracking-wide">{{ __('checkout.success.heading') }}</h1>
            <p class="text-sm text-gray-500">
                {{ __('checkout.success.number') }}: <span class="font-semibold text-black">{{ $order->order_number }}</span>
            </p>
        </div>

        <div class="flex flex-col gap-6 border border-black/10 rounded-xl p-6">
            {{-- Status --}}
            <div class="flex items-center justify-between pb-4 border-b border-black/10">
                <span class="text-sm text-gray-500">{{ __('order.status_title') }}</span>
                @php
                    $badge =
                        $order->status === 'cancelled'
                            ? ['bg-red-50 text-red-600', 'bg-red-500']
                            : ['bg-amber-50 text-amber-600', 'bg-amber-500'];
                @endphp
                <span
                    class="inline-flex items-center gap-1.5 {{ $badge[0] }} text-xs font-semibold uppercase tracking-wide px-3 py-1.5 rounded-full">
                    <span class="w-1.5 h-1.5 rounded-full {{ $badge[1] }}"></span>
                    {{ $order->localizedStatus() }}
                </span>
            </div>

            {{-- Data penerima --}}
            <div class="flex flex-col gap-1">
                <h2 class="text-sm font-bold uppercase tracking-widest text-black/50 mb-2">{{ __('order.recipient') }}</h2>
                <p class="text-sm"><span class="text-gray-500">{{ __('order.name') }}:</span> {{ $order->customer_name }}
                </p>
                <p class="text-sm"><span class="text-gray-500">{{ __('order.phone') }}:</span>
                    {{ $order->customer_phone }}</p>
                @if ($order->customer_email)
                    <p class="text-sm"><span class="text-gray-500">{{ __('order.email') }}:</span>
                        {{ $order->customer_email }}</p>
                @endif
                <p class="text-sm"><span class="text-gray-500">{{ __('order.address') }}:</span>
                    {{ $order->customer_address }}</p>
            </div>

            {{-- Rincian barang --}}
            <div class="flex flex-col gap-4">
                <h2 class="text-sm font-bold uppercase tracking-widest text-black/50">{{ __('order.details') }}</h2>

                <div class="flex flex-col divide-y divide-black/5">
                    @foreach ($order->items as $item)
                        <div class="flex items-center justify-between py-3 first:pt-0">
                            <div class="flex flex-col">
                                <span class="text-sm font-semibold">{{ $item->product_name }}</span>
                                @if ($item->variant_label)
                                    <span class="text-xs text-gray-500">{{ __('order.size') }}:
                                        {{ $item->variant_label }}</span>
                                @endif
                                <span class="text-xs text-gray-500">{{ $item->quantity }}x @
                                    Rp{{ number_format($item->price, 0, ',', '.') }}</span>
                            </div>
                            <span class="text-sm font-semibold">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Ringkasan biaya --}}
            <div class="flex flex-col gap-2 pt-4 border-t border-black/10">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">{{ __('order.total_weight') }}</span>
                    <span class="text-gray-600">{{ __('order.grams', ['count' => $order->total_weight]) }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">{{ __('order.subtotal') }}</span>
                    <span class="font-semibold">Rp{{ number_format($order->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">{{ __('order.courier') }}</span>
                    <span class="text-gray-600">{{ $order->shipping_courier }} - {{ $order->shipping_service }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">{{ __('order.shipping') }}</span>
                    <span class="font-semibold">Rp{{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between text-base font-bold pt-2 border-t border-black/10">
                    <span>{{ __('order.total') }}</span>
                    <span>Rp{{ number_format($order->total, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
            @if ($order->user_id)
                <a href="{{ route('account.orders.show', $order) }}"
                    class="flex-1 text-center border border-black/15 hover:bg-black/5 text-black uppercase font-bold tracking-widest py-3.5 rounded-lg transition">
                    {{ __('checkout.success.view_order') }}
                </a>
            @endif
            <a href="{{ route('home') }}"
                class="flex-1 text-center bg-black hover:bg-black/80 text-white uppercase font-bold tracking-widest py-3.5 rounded-lg transition">
                {{ __('checkout.success.continue') }}
            </a>
        </div>

    </section>
@endsection
