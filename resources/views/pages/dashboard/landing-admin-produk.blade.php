@extends('template.dashboard.layout')
@section('content')
    <main class="flex flex-col items-center bg-black text-white w-full min-h-screen mb-24">
        @include('components/dashboard/navbar')
        @include('components/dashboard/role-header')

        <div class="flex flex-col w-full max-w-4xl gap-6 px-6 lg:px-14 pb-14">
            {{-- Ringkasan --}}
            <div class="grid grid-cols-3 gap-3">
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
                    <span class="text-white/40 text-[11px] uppercase tracking-wide">Stok Habis</span>
                    <span class="text-2xl font-bold {{ $outOfStockCount > 0 ? 'text-[#e05656]' : '' }}">
                        {{ $outOfStockCount }}
                    </span>
                </div>
            </div>

            @if ($lowStockCount > 0 || $outOfStockCount > 0)
                <div
                    class="flex items-center gap-3 bg-amber-400/5 border border-amber-400/20 rounded-2xl px-5 py-4 text-sm text-amber-200/90">
                    <i class="bi bi-exclamation-triangle-fill text-amber-400 shrink-0"></i>
                    <span>Ada produk yang stoknya menipis atau habis — cek & update di halaman Kelola Produk.</span>
                </div>
            @endif
        </div>
    </main>
@endsection
