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

            {{-- Grafik proporsi kondisi stok --}}
            <div class="bg-[#0D0D0D] border border-white/10 rounded-2xl p-5 flex flex-col gap-4">
                <h2 class="text-white/40 text-[11px] font-semibold uppercase tracking-widest">
                    Kondisi Stok ({{ $totalVariants }} varian)
                </h2>

                @if ($totalVariants > 0)
                    <div class="w-full h-3 rounded-full overflow-hidden flex bg-white/5">
                        <div class="h-full bg-green-500" style="width: {{ ($safeVariantCount / $totalVariants) * 100 }}%">
                        </div>
                        <div class="h-full bg-amber-400" style="width: {{ ($lowStockCount / $totalVariants) * 100 }}%">
                        </div>
                        <div class="h-full bg-[#B71C1C]" style="width: {{ ($outOfStockCount / $totalVariants) * 100 }}%">
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-x-5 gap-y-2 text-xs">
                        <span class="flex items-center gap-1.5 text-white/60">
                            <span class="w-2 h-2 rounded-full bg-green-500"></span> Aman ({{ $safeVariantCount }})
                        </span>
                        <span class="flex items-center gap-1.5 text-white/60">
                            <span class="w-2 h-2 rounded-full bg-amber-400"></span> Menipis ({{ $lowStockCount }})
                        </span>
                        <span class="flex items-center gap-1.5 text-white/60">
                            <span class="w-2 h-2 rounded-full bg-[#B71C1C]"></span> Habis ({{ $outOfStockCount }})
                        </span>
                    </div>
                @else
                    <p class="text-white/30 text-sm">Belum ada varian produk.</p>
                @endif
            </div>

            {{-- Preview produk paling kritis --}}
            <div class="bg-[#0D0D0D] border border-white/10 rounded-2xl p-5 flex flex-col gap-3">
                <div class="flex items-center justify-between">
                    <h2 class="text-white/40 text-[11px] font-semibold uppercase tracking-widest">
                        Perlu Perhatian
                    </h2>
                    <a href="{{ route('dashboard.clothes') }}"
                        class="text-white/40 hover:text-white text-xs transition">Lihat semua</a>
                </div>

                @forelse ($criticalVariants as $variant)
                    <a href="{{ route('dashboard.clothes', ['edit' => optional($variant->product)->id_product]) }}#product-{{ optional($variant->product)->id_product }}"
                        class="flex items-center gap-3 py-2 {{ !$loop->last ? 'border-b border-white/6' : '' }} hover:bg-white/5 -mx-2 px-2 rounded-lg transition">
                        <div class="w-9 h-9 rounded-lg bg-white/5 overflow-hidden shrink-0">
                            @if (optional($variant->product)->images && $variant->product->images->first())
                                <img src="{{ Storage::url($variant->product->images->first()->image_path) }}"
                                    alt="{{ $variant->product->name }}" class="w-full h-full object-cover">
                            @endif
                        </div>
                        <div class="flex flex-col min-w-0 flex-1">
                            <span class="text-sm font-semibold truncate">
                                {{ $variant->product->name ?? 'Produk tidak ditemukan' }}
                            </span>
                            <span class="text-white/40 text-[11px]">Ukuran {{ $variant->label }}</span>
                        </div>
                        <span
                            class="text-[11px] font-semibold px-2 py-1 rounded-md shrink-0 {{ $variant->stock === 0 ? 'text-[#e05656] bg-[#B71C1C]/10' : 'text-amber-400 bg-amber-400/10' }}">
                            {{ $variant->stock === 0 ? 'Habis' : $variant->stock . ' pcs' }}
                        </span>
                    </a>
                @empty
                    <p class="text-white/30 text-sm py-4 text-center">Semua stok aman, gak ada yang perlu buru-buru.</p>
                @endforelse
            </div>
        </div>
    </main>
@endsection
