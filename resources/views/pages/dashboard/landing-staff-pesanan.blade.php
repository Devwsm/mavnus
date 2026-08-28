@extends('template.dashboard.layout')
@section('content')
    <main class="flex flex-col items-center bg-black text-white w-full min-h-screen mb-24">
        @include('components/dashboard/navbar')
        @include('components/dashboard/role-header')

        <div class="flex flex-col w-full max-w-4xl gap-6 px-6 lg:px-14 pb-14">
            {{-- Ringkasan pesanan per status --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                <a href="{{ route('dashboard.orders', ['status' => 'pending']) }}"
                    class="bg-[#0D0D0D] border border-white/10 hover:border-white/20 rounded-2xl p-4 flex flex-col gap-1 transition">
                    <span class="text-white/40 text-[11px] uppercase tracking-wide">Pending</span>
                    <span class="text-2xl font-bold {{ $orderCounts['pending'] > 0 ? 'text-[#e05656]' : '' }}">
                        {{ $orderCounts['pending'] }}
                    </span>
                </a>
                <a href="{{ route('dashboard.orders', ['status' => 'processing']) }}"
                    class="bg-[#0D0D0D] border border-white/10 hover:border-white/20 rounded-2xl p-4 flex flex-col gap-1 transition">
                    <span class="text-white/40 text-[11px] uppercase tracking-wide">Diproses</span>
                    <span class="text-2xl font-bold {{ $orderCounts['processing'] > 0 ? 'text-amber-400' : '' }}">
                        {{ $orderCounts['processing'] }}
                    </span>
                </a>
                <a href="{{ route('dashboard.orders', ['status' => 'shipped']) }}"
                    class="bg-[#0D0D0D] border border-white/10 hover:border-white/20 rounded-2xl p-4 flex flex-col gap-1 transition">
                    <span class="text-white/40 text-[11px] uppercase tracking-wide">Dikirim</span>
                    <span class="text-2xl font-bold">{{ $orderCounts['shipped'] }}</span>
                </a>
                <a href="{{ route('dashboard.orders', ['status' => 'completed']) }}"
                    class="bg-[#0D0D0D] border border-white/10 hover:border-white/20 rounded-2xl p-4 flex flex-col gap-1 transition">
                    <span class="text-white/40 text-[11px] uppercase tracking-wide">Selesai</span>
                    <span class="text-2xl font-bold text-green-400">{{ $orderCounts['completed'] }}</span>
                </a>
            </div>

            @if ($orderCounts['pending'] > 0)
                <div
                    class="flex items-center gap-3 bg-[#B71C1C]/5 border border-[#B71C1C]/20 rounded-2xl px-5 py-4 text-sm text-red-200/90">
                    <i class="bi bi-exclamation-triangle-fill text-[#e05656] shrink-0"></i>
                    <span>Ada {{ $orderCounts['pending'] }} pesanan yang masih pending, cek & proses di Kelola
                        Pesanan.</span>
                </div>
            @endif

            {{-- Quick link --}}
            <div>
                <h2 class="text-white/40 text-xs font-semibold uppercase tracking-widest mb-3">Menu</h2>
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('dashboard.orders') }}"
                        class="flex flex-col items-center justify-center gap-2 bg-[#0D0D0D] border border-white/10 hover:border-[#B71C1C]/50 hover:bg-[#B71C1C]/5 rounded-2xl p-6 text-center transition group">
                        <i class="bi bi-box-seam text-2xl text-white/70 group-hover:text-[#B71C1C] transition"></i>
                        <span class="text-sm font-semibold">Kelola Pesanan</span>
                    </a>
                    <a href="{{ route('dashboard.import-export') }}"
                        class="flex flex-col items-center justify-center gap-2 bg-[#0D0D0D] border border-white/10 hover:border-[#B71C1C]/50 hover:bg-[#B71C1C]/5 rounded-2xl p-6 text-center transition group">
                        <i
                            class="bi bi-file-earmark-excel text-2xl text-white/70 group-hover:text-[#B71C1C] transition"></i>
                        <span class="text-sm font-semibold">Export Invoice</span>
                    </a>
                </div>
            </div>
        </div>
    </main>
@endsection
