{{--
    Halaman "Riwayat Pesanan" - gaya "app shell" sama kayak account.blade.php.
    Ada chip filter status (Semua/Menunggu/Diproses/Dikirim/Selesai/Dibatalkan)
    yang nyambung ke query ?status= di accountController::orders().
--}}
@extends('template.account-layout')
@section('content')
    <section id="main-content"
        class="flex flex-col w-full bg-[#F5F6F8] gap-4 lg:gap-10 p-3 sm:p-6 lg:p-14 pt-24 md:pt-22 lg:pt-32 pb-24 lg:pb-14 min-h-screen">
        @include('components/errors/alerts')

        <div class="hidden lg:block">
            <h1 class="text-2xl md:text-3xl font-bold uppercase tracking-wide">Riwayat Pesanan</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola informasi & pesanan kamu di sini.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-8">
            {{-- Menu / sidebar --}}
            <div class="lg:col-span-1 order-1">
                <div class="lg:sticky lg:top-28">
                    @include('components.account.menu', ['active' => 'orders'])
                </div>
            </div>

            {{-- Konten: filter chip + list order --}}
            <div class="lg:col-span-2 order-2 flex flex-col gap-4">
                {{-- Chip filter status --}}
                @php
                    $filters = [
                        'all' => 'Semua',
                        'pending' => 'Menunggu',
                        'processing' => 'Diproses',
                        'shipped' => 'Dikirim',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                    ];
                    $activeFilter = $statusFilter ?? 'all';
                @endphp
                <div class="flex gap-2 overflow-x-auto pb-1 -mx-3 px-3 sm:mx-0 sm:px-0 no-scrollbar">
                    @foreach ($filters as $value => $label)
                        <a href="{{ route('account.orders', $value === 'all' ? [] : ['status' => $value]) }}"
                            class="shrink-0 px-4 py-2 rounded-full text-xs font-semibold whitespace-nowrap transition
                                {{ $activeFilter === $value ? 'bg-black text-white' : 'bg-white text-gray-500 shadow-sm shadow-black/5 hover:text-black' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>

                @forelse ($orders as $order)
                    <a href="{{ route('order.success', $order) }}"
                        class="flex flex-col gap-4 bg-white rounded-2xl shadow-sm shadow-black/5 p-4 sm:p-6 hover:shadow-md active:scale-[0.99] transition">
                        <div class="flex items-center justify-between gap-2 flex-wrap pb-4 border-b border-gray-100">
                            <div>
                                <p class="text-sm font-semibold">{{ $order->order_number }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    {{ $order->created_at->translatedFormat('d F Y, H:i') }}</p>
                            </div>
                            @php
                                $statusStyle = match ($order->status) {
                                    'completed' => ['bg-green-50', 'text-green-600', 'bg-green-500'],
                                    'cancelled' => ['bg-red-50', 'text-red-600', 'bg-red-500'],
                                    'shipped', 'processing' => ['bg-blue-50', 'text-blue-600', 'bg-blue-500'],
                                    default => ['bg-amber-50', 'text-amber-600', 'bg-amber-500'],
                                };
                            @endphp
                            <span
                                class="inline-flex items-center gap-1.5 {{ $statusStyle[0] }} {{ $statusStyle[1] }} text-xs font-semibold px-3 py-1.5 rounded-full shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full {{ $statusStyle[2] }}"></span>
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>

                        <div class="flex flex-col gap-2">
                            @foreach ($order->items->take(2) as $item)
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">{{ $item->quantity }}x {{ $item->product_name }}</span>
                                    <span class="font-semibold">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                            @if ($order->items->count() > 2)
                                <p class="text-xs text-gray-400">+{{ $order->items->count() - 2 }} produk lainnya</p>
                            @endif
                        </div>

                        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                            <span class="text-sm text-gray-500">Total Pesanan</span>
                            <span class="text-base font-bold">Rp{{ number_format($order->total, 0, ',', '.') }}</span>
                        </div>
                    </a>
                @empty
                    <div
                        class="flex flex-col items-center text-center gap-3 bg-white rounded-2xl shadow-sm shadow-black/5 p-8 sm:p-10">
                        <span
                            class="w-14 h-14 rounded-full bg-gray-50 flex items-center justify-center text-2xl text-gray-300">
                            <i class="bi bi-bag"></i>
                        </span>
                        <p class="text-sm text-gray-500">
                            @if ($statusFilter)
                                Belum ada pesanan dengan status "{{ $filters[$statusFilter] ?? ucfirst($statusFilter) }}".
                            @else
                                Kamu belum punya pesanan apa pun.
                            @endif
                        </p>
                        <a href="{{ route('home') }}"
                            class="text-sm font-semibold underline underline-offset-4 decoration-black/30 hover:decoration-black transition">
                            Mulai belanja
                        </a>
                    </div>
                @endforelse

                @if ($orders->hasPages())
                    <div class="mt-2">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
