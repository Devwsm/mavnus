@extends('template.dashboard.layout')
@section('content')
    <main class="flex flex-col items-center bg-black text-white w-full min-h-screen mb-24">
        @include('components/dashboard/navbar')
        @include('components/dashboard/role-header')

        <div class="flex flex-col w-full max-w-4xl gap-6 px-6 lg:px-14 pb-14">
            {{-- Ringkasan --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                <div class="bg-[#0D0D0D] border border-white/10 rounded-2xl p-4 flex flex-col gap-1">
                    <span class="text-white/40 text-[11px] uppercase tracking-wide">Produk Aktif</span>
                    <span class="text-2xl font-bold">{{ $totalProdukAktif }}</span>
                </div>
                <div class="bg-[#0D0D0D] border border-white/10 rounded-2xl p-4 flex flex-col gap-1">
                    <span class="text-white/40 text-[11px] uppercase tracking-wide">Stok Menipis</span>
                    <span class="text-2xl font-bold {{ $lowStockCount > 0 ? 'text-amber-400' : '' }}">
                        {{ $lowStockCount }}
                    </span>
                </div>
                <div class="bg-[#0D0D0D] border border-white/10 rounded-2xl p-4 flex flex-col gap-1">
                    <span class="text-white/40 text-[11px] uppercase tracking-wide">Pesanan Pending</span>
                    <span class="text-2xl font-bold {{ $pesananPendingCount > 0 ? 'text-[#e05656]' : '' }}">
                        {{ $pesananPendingCount }}
                    </span>
                </div>
                <div class="bg-[#0D0D0D] border border-white/10 rounded-2xl p-4 flex flex-col gap-1">
                    <span class="text-white/40 text-[11px] uppercase tracking-wide">Pengunjung Hari Ini</span>
                    <span class="text-2xl font-bold">{{ $visitsToday }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
                {{-- Grafik pesanan 7 hari terakhir --}}
                <div class="lg:col-span-2 bg-[#0D0D0D] border border-white/10 rounded-2xl p-5 flex flex-col gap-4">
                    <h2 class="text-white/40 text-[11px] font-semibold uppercase tracking-widest">
                        Pesanan 7 Hari Terakhir
                    </h2>
                    <div class="flex items-end justify-between gap-2 h-28">
                        @foreach ($ordersTrend as $day)
                            @php $heightPct = max(4, ($day['count'] / $ordersTrendMax) * 100); @endphp
                            <div class="flex-1 flex flex-col items-center gap-1.5 h-full justify-end">
                                <span class="text-white/50 text-[10px] font-semibold">{{ $day['count'] }}</span>
                                <div class="w-full rounded-t-md {{ $day['count'] > 0 ? 'bg-[#B71C1C]' : 'bg-white/10' }}"
                                    style="height: {{ $heightPct }}%"></div>
                                <span class="text-white/30 text-[10px]">{{ $day['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Preview pesanan terbaru --}}
                <div class="lg:col-span-3 bg-[#0D0D0D] border border-white/10 rounded-2xl p-5 flex flex-col gap-3">
                    <div class="flex items-center justify-between">
                        <h2 class="text-white/40 text-[11px] font-semibold uppercase tracking-widest">
                            Pesanan Terbaru
                        </h2>
                        <a href="{{ route('dashboard.orders') }}"
                            class="text-white/40 hover:text-white text-xs transition">Lihat semua</a>
                    </div>

                    @forelse ($recentOrders as $order)
                        @php
                            $statusStyle = match ($order->status) {
                                'pending' => 'text-[#B77B1C] bg-[#B77B1C]/10',
                                'processing' => 'text-[#1C1CB7] bg-[#1C1CB7]/10',
                                'shipped' => 'text-[#5E1C5E] bg-[#5E1C5E]/10',
                                'completed' => 'text-[#1C7B1C] bg-[#1C7B1C]/10',
                                'cancelled' => 'text-white/40 bg-white/5',
                                default => 'text-white/40 bg-white/5',
                            };
                        @endphp
                        <a href="{{ route('dashboard.orders.show', $order) }}"
                            class="flex items-center justify-between gap-3 py-2.5 {{ !$loop->last ? 'border-b border-white/6' : '' }} hover:bg-white/5 -mx-2 px-2 rounded-lg transition">
                            <div class="flex flex-col min-w-0">
                                <span class="text-sm font-semibold truncate">{{ $order->customer_name }}</span>
                                <span class="text-white/30 text-[11px]">{{ $order->order_number }}</span>
                            </div>
                            <div class="flex items-center gap-2.5 shrink-0">
                                <span
                                    class="text-xs font-semibold">Rp{{ number_format($order->total, 0, ',', '.') }}</span>
                                <span class="text-[10px] font-semibold uppercase px-2 py-1 rounded-md {{ $statusStyle }}">
                                    {{ $order->status }}
                                </span>
                            </div>
                        </a>
                    @empty
                        <p class="text-white/30 text-sm py-4 text-center">Belum ada pesanan masuk.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </main>
@endsection
