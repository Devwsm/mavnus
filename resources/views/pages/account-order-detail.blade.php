{{--
    Halaman "Detail Pesanan" - dipisah dari order-success (yang khusus buat
    layar konfirmasi abis checkout). Ini yang dibuka kalau user klik salah
    satu pesanan di Riwayat Pesanan, gaya "app shell" sama kayak halaman
    akun lainnya, dengan status badge dinamis (bukan cuma amber statis).
--}}
@php
    $active = 'orders';
@endphp
@extends('template.account-layout')
@section('content')
    <section id="main-content"
        class="flex flex-col w-full bg-[#F5F6F8] gap-4 p-3 sm:p-6 lg:p-14 pt-24 md:pt-22 lg:pt-32 pb-24 lg:pb-14 min-h-screen">
        @include('components/errors/alerts')

        {{-- Top bar: back + judul --}}
        <div
            class="flex items-center gap-3 bg-white rounded-2xl shadow-sm shadow-black/5 px-4 py-3.5 lg:bg-transparent lg:shadow-none lg:px-0 lg:py-0">
            <a href="{{ route('account.orders') }}" aria-label="Kembali ke Riwayat Pesanan"
                class="w-9 h-9 rounded-full bg-gray-100 lg:border lg:border-black/10 lg:bg-white flex items-center justify-center shrink-0 hover:bg-gray-200 lg:hover:bg-gray-50 active:scale-95 transition">
                <i class="bi bi-arrow-left text-lg"></i>
            </a>
            <div class="min-w-0 flex-1">
                <h1 class="text-base sm:text-xl lg:text-3xl font-bold lg:uppercase lg:tracking-wide truncate">
                    {{ $order->order_number }}
                </h1>
                <p class="hidden lg:block text-sm text-gray-500 mt-1">
                    {{ $order->created_at->translatedFormat('d F Y, H:i') }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-8">
            {{-- Menu / sidebar --}}
            <div class="lg:col-span-1 order-1">
                <div class="lg:sticky lg:top-28">
                    @include('components.account.menu', ['active' => 'orders'])
                </div>
            </div>

            {{-- Konten detail pesanan --}}
            <div class="lg:col-span-2 order-2 flex flex-col gap-4">
                {{-- Status --}}
                @php
                    $statusStyle = match ($order->status) {
                        'completed' => ['bg-green-50', 'text-green-600', 'bg-green-500'],
                        'cancelled' => ['bg-red-50', 'text-red-600', 'bg-red-500'],
                        'shipped', 'processing' => ['bg-blue-50', 'text-blue-600', 'bg-blue-500'],
                        default => ['bg-amber-50', 'text-amber-600', 'bg-amber-500'],
                    };
                @endphp
                <div class="bg-white rounded-2xl shadow-sm shadow-black/5 p-4 sm:p-5 flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Status Pesanan</p>
                        <p class="text-sm font-semibold lg:hidden mt-1">
                            {{ $order->created_at->translatedFormat('d F Y, H:i') }}
                        </p>
                    </div>
                    <span
                        class="inline-flex items-center gap-1.5 {{ $statusStyle[0] }} {{ $statusStyle[1] }} text-xs font-semibold uppercase tracking-wide px-3 py-1.5 rounded-full shrink-0">
                        <span class="w-1.5 h-1.5 rounded-full {{ $statusStyle[2] }}"></span>
                        {{ ucfirst($order->status) }}
                    </span>
                </div>

                {{-- Data penerima --}}
                <div class="bg-white rounded-2xl shadow-sm shadow-black/5 p-4 sm:p-5 flex flex-col gap-2.5">
                    <h2 class="text-sm font-bold mb-1">Data Penerima</h2>
                    <p class="text-sm"><span class="text-gray-500">Nama:</span> {{ $order->customer_name }}</p>
                    <p class="text-sm"><span class="text-gray-500">No. HP:</span> {{ $order->customer_phone }}</p>
                    <p class="text-sm"><span class="text-gray-500">Alamat:</span> {{ $order->customer_address }}</p>
                </div>

                {{-- Rincian barang --}}
                <div class="bg-white rounded-2xl shadow-sm shadow-black/5 p-4 sm:p-5 flex flex-col gap-1">
                    <h2 class="text-sm font-bold mb-2">Rincian Pesanan</h2>
                    <div class="flex flex-col divide-y divide-gray-100">
                        @foreach ($order->items as $item)
                            <div class="flex items-center gap-3 py-3 first:pt-0">
                                <div class="w-12 h-12 rounded-lg bg-gray-50 overflow-hidden shrink-0">
                                    @if ($item->product_image)
                                        <img src="{{ Storage::url($item->product_image) }}"
                                            alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <div class="flex flex-col min-w-0 flex-1">
                                    <span class="text-sm font-semibold truncate">{{ $item->product_name }}</span>
                                    @if ($item->variant_label)
                                        <span class="text-xs text-gray-500">Size: {{ $item->variant_label }}</span>
                                    @endif
                                    <span class="text-xs text-gray-500">{{ $item->quantity }}x
                                        Rp{{ number_format($item->price, 0, ',', '.') }}</span>
                                </div>
                                <span class="text-sm font-semibold shrink-0">
                                    Rp{{ number_format($item->subtotal, 0, ',', '.') }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Ringkasan biaya --}}
                <div class="bg-white rounded-2xl shadow-sm shadow-black/5 p-4 sm:p-5 flex flex-col gap-2">
                    <h2 class="text-sm font-bold mb-1">Ringkasan Biaya</h2>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Total Berat</span>
                        <span class="text-gray-600">{{ $order->total_weight }} gram</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Subtotal</span>
                        <span class="font-semibold">Rp{{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Kurir</span>
                        <span class="text-gray-600">{{ $order->shipping_courier }} - {{ $order->shipping_service }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Ongkir</span>
                        <span class="font-semibold">Rp{{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between text-base font-bold pt-2.5 mt-1 border-t border-gray-100">
                        <span>Total</span>
                        <span>Rp{{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
