@extends('template.dashboard.layout')
@section('content')
    @php
        $rupiah = fn($amount) => 'Rp' . number_format($amount, 0, ',', '.');
    @endphp
    <div class="flex flex-col items-center bg-black text-white w-full min-h-screen mb-24">
        @include('components/dashboard/navbar')

        <div class="flex flex-col items-center justify-center p-8 text-center">
            <h1 class="text-3xl font-bold">Anggota</h1>
            <p class="text-white/50 text-sm mt-2 max-w-md">
                Pembeli yang sudah membuat akun. Pembeli tanpa akun (guest) tidak tercatat di sini.
            </p>
        </div>

        <div class="flex flex-col w-full max-w-5xl gap-5 px-6 lg:px-14 pb-14">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <x-dashboard.stat-card label="Total Anggota" :value="$stats['total']" />
                <x-dashboard.stat-card label="Baru Bulan Ini" :value="$stats['newMonth']" />
                <x-dashboard.stat-card label="Pernah Memesan" :value="$stats['withOrders']" :note="$stats['total'] > 0
                    ? round(($stats['withOrders'] / $stats['total']) * 100) . '% dari seluruh anggota'
                    : null" />
            </div>

            <form method="GET" action="{{ route('dashboard.members') }}"
                class="bg-[#0D0D0D] border border-white/10 rounded-2xl p-5 flex flex-col sm:flex-row gap-3 sm:items-end">
                <div class="flex-1">
                    <label for="member-q"
                        class="block text-white/40 text-[11px] uppercase tracking-wide mb-1.5">Cari</label>
                    <input type="search" id="member-q" name="q" value="{{ $q }}" maxlength="100"
                        placeholder="Nama, email, atau nomor HP"
                        class="w-full bg-black border border-white/10 rounded-lg px-3 py-2.5 text-white text-sm focus:outline-none focus:border-[#B71C1C]">
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit"
                        class="flex items-center justify-center gap-1.5 bg-[#B71C1C] hover:bg-[#891212] text-white text-sm font-semibold uppercase tracking-wide px-6 py-2.5 rounded-lg transition">
                        <i class="bi bi-search"></i>
                        Cari
                    </button>
                    @if ($q !== '')
                        <a href="{{ route('dashboard.members') }}"
                            class="text-[#e05656] hover:underline text-sm px-2 py-2.5">Reset</a>
                    @endif
                </div>
            </form>

            <x-dashboard.panel :title="$q !== '' ? 'Hasil pencarian (' . $members->total() . ')' : 'Daftar Anggota (' . $members->total() . ')'" class="gap-1">
                @forelse ($members as $member)
                    <a href="{{ route('dashboard.members.show', $member) }}"
                        class="flex items-center gap-3 py-3 {{ !$loop->last ? 'border-b border-white/6' : '' }} hover:bg-white/5 -mx-2 px-2 rounded-lg transition">
                        <x-dashboard.avatar :name="$member->name" />
                        <div class="flex flex-col min-w-0 flex-1">
                            <span class="text-sm font-semibold truncate">{{ $member->name }}</span>
                            <span class="text-white/40 text-[11px] truncate">{{ $member->email }}</span>
                            <span
                                class="text-white/30 text-[11px] sm:hidden">{{ $member->phone ?: 'Belum ada nomor HP' }}</span>
                        </div>
                        <span class="hidden sm:block text-white/50 text-xs w-32 truncate">{{ $member->phone ?: '-' }}</span>
                        <span class="hidden md:block text-white/40 text-xs w-28">
                            {{ $member->created_at->translatedFormat('d M Y') }}
                        </span>
                        <div class="flex flex-col items-end shrink-0 w-28">
                            <span class="text-xs font-semibold">{{ $member->orders_count }} pesanan</span>
                            <span
                                class="text-green-400 text-[11px] font-semibold">{{ $rupiah($member->total_spent ?? 0) }}</span>
                        </div>
                    </a>
                @empty
                    <div class="flex flex-col items-center gap-2 py-16">
                        <i class="bi bi-person-lines-fill text-white/15 text-4xl"></i>
                        <p class="text-white/30 text-sm">
                            {{ $q !== '' ? 'Tidak ada anggota yang cocok dengan pencarian.' : 'Belum ada anggota yang mendaftar.' }}
                        </p>
                    </div>
                @endforelse
            </x-dashboard.panel>

            @if ($members->hasPages())
                <div class="mt-2">
                    {{ $members->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
