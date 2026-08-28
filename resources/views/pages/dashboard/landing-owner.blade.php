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
        </div>
    </main>
@endsection
