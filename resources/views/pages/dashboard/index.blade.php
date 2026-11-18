@extends('template.dashboard.layout')
@section('content')
    @php
        $rupiah = fn($amount) => 'Rp' . number_format($amount, 0, ',', '.');
        $rowClass = 'border-b border-white/5';

        // Hal yang perlu ditindak staf. Kosong = semuanya beres.
        $attention = collect([
            [
                'show' => $pendingOrders > 0,
                'text' => $pendingOrders . ' pesanan menunggu diproses',
                'href' => route('dashboard.orders', ['status' => 'pending']),
                'tone' => 'text-amber-300',
            ],
            [
                'show' => $outOfStockCount > 0,
                'text' => $outOfStockCount . ' stok habis',
                'href' => route('dashboard.produk'),
                'tone' => 'text-[#e05656]',
            ],
            [
                'show' => $lowStockCount > 0,
                'text' => $lowStockCount . ' stok menipis',
                'href' => route('dashboard.produk'),
                'tone' => 'text-amber-300',
            ],
        ])->where('show');
    @endphp

    <main class="flex flex-col items-center bg-black text-white w-full min-h-screen mb-24">
        @include('components/dashboard/navbar')

        <div class="flex flex-col items-center gap-1 p-8 text-center">
            <h1 class="text-2xl md:text-3xl font-bold">Halo, {{ session('name') ?: session('user') }}</h1>
            <p class="text-white/40 text-sm">{{ now()->translatedFormat('l, d F Y') }}</p>
        </div>

        <div class="flex flex-col w-full max-w-4xl gap-5 px-6 lg:px-14 pb-14">
            {{-- 1. Apa yang perlu ditindak sekarang --}}
            @if ($attention->isNotEmpty())
                <div class="rounded-2xl border border-amber-300/20 bg-amber-300/5 px-5 py-4 flex flex-col gap-2">
                    <span class="text-white/80 text-sm font-semibold">Perlu ditindak</span>
                    <div class="flex flex-wrap gap-x-6 gap-y-1.5">
                        @foreach ($attention as $item)
                            <a href="{{ $item['href'] }}"
                                class="{{ $item['tone'] }} text-sm hover:underline underline-offset-4 inline-flex items-center gap-1.5">
                                {{ $item['text'] }} <i class="bi bi-arrow-right text-xs"></i>
                            </a>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="rounded-2xl border border-emerald-400/15 bg-emerald-400/5 px-5 py-4 flex items-center gap-3">
                    <i class="bi bi-check-circle text-emerald-400 text-lg"></i>
                    <span class="text-white/70 text-sm">Semua beres. Tidak ada pesanan menunggu atau stok yang perlu
                        dicek.</span>
                </div>
            @endif

            {{-- 2. Angka utama (tiap kartu bisa diklik ke halamannya) --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                <x-dashboard.stat-card label="Omzet bulan ini" :value="$rupiah($omzetBulanIni)" value-class="text-emerald-400"
                    note="Dari pesanan yang sudah selesai" />
                <x-dashboard.stat-card label="Pesanan" :value="$totalOrders" :href="route('dashboard.orders')" :note="$pendingOrders > 0 ? $pendingOrders . ' menunggu diproses' : 'Tidak ada yang menunggu'" />
                <x-dashboard.stat-card label="Produk aktif" :value="$totalProdukAktif" :href="route('dashboard.produk')" :note="$scheduledCount > 0 ? $scheduledCount . ' rilis terjadwal' : 'Tampil di toko'" />
                <x-dashboard.stat-card label="Anggota" :value="$totalMembers" :href="route('dashboard.members')" :note="$newMembersThisMonth > 0
                    ? '+' . $newMembersThisMonth . ' bulan ini'
                    : 'Belum ada yang baru bulan ini'" />
            </div>

            {{-- 3. Pesanan terbaru + stok yang perlu dicek --}}
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
                <x-dashboard.panel title="Pesanan terbaru" :href="route('dashboard.orders')" link="Lihat semua" class="lg:col-span-3">
                    @forelse ($recentOrders as $order)
                        <a href="{{ route('dashboard.orders.show', $order) }}"
                            class="flex items-center justify-between gap-3 py-2.5 {{ !$loop->last ? $rowClass : '' }} hover:bg-white/5 -mx-2 px-2 rounded-lg transition">
                            <div class="flex flex-col min-w-0">
                                <span class="text-sm font-medium truncate">{{ $order->customer_name }}</span>
                                <span class="text-white/35 text-[11px]">{{ $order->order_number }}</span>
                            </div>
                            <div class="flex items-center gap-2.5 shrink-0">
                                <span class="text-xs text-white/70">{{ $rupiah($order->total) }}</span>
                                <x-dashboard.status-badge :order="$order" />
                            </div>
                        </a>
                    @empty
                        <x-dashboard.empty icon="bi-receipt" title="Belum ada pesanan"
                            text="Pesanan dari toko akan muncul di sini begitu pembeli checkout." />
                    @endforelse
                </x-dashboard.panel>

                <x-dashboard.panel title="Stok perlu dicek" :href="route('dashboard.produk')" link="Kelola produk" class="lg:col-span-2">
                    @forelse ($criticalItems as $item)
                        @php $productId = $item['product']?->id_product; @endphp
                        <a href="{{ $productId ? route('dashboard.produk', ['edit' => $productId]) . '#product-' . $productId : route('dashboard.produk') }}"
                            class="flex items-center gap-3 py-2 {{ !$loop->last ? $rowClass : '' }} hover:bg-white/5 -mx-2 px-2 rounded-lg transition">
                            <x-dashboard.thumb :path="$item['product']?->images->first()?->image_path" :alt="$item['product']?->name" />
                            <div class="flex flex-col min-w-0 flex-1">
                                <span class="text-sm font-medium truncate">
                                    {{ $item['product']?->name ?? 'Produk tidak ditemukan' }}
                                </span>
                                <span class="text-white/40 text-[11px]">{{ $item['label'] }}</span>
                            </div>
                            <span
                                class="text-[11px] font-medium px-2 py-1 rounded-md shrink-0 {{ $item['stock'] === 0 ? 'text-[#e05656] bg-[#B71C1C]/10' : 'text-amber-300 bg-amber-300/10' }}">
                                {{ $item['stock'] === 0 ? 'Habis' : 'Sisa ' . $item['stock'] }}
                            </span>
                        </a>
                    @empty
                        <x-dashboard.empty icon="bi-box-seam" title="Stok aman"
                            text="Produk yang stoknya habis atau tinggal sedikit akan muncul di sini." />
                    @endforelse
                </x-dashboard.panel>
            </div>

            {{-- 4. Penjualan: baru tampil kalau sudah ada pesanan selesai, supaya tidak ada grafik kosong --}}
            @if ($hasSales)
                <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
                    <x-dashboard.panel title="Omzet 6 bulan terakhir" class="lg:col-span-3">
                        <x-dashboard.bar-chart color="bg-emerald-500/70" :max="$omzetPerBulanMax" :items="$omzetPerBulan->map(
                            fn($bulan) => [
                                'label' => $bulan['label'],
                                'value' => $bulan['total'],
                                'text' =>
                                    $bulan['total'] > 0
                                        ? 'Rp' . number_format($bulan['total'] / 1000, 0, ',', '.') . 'rb'
                                        : '-',
                            ],
                        )" />
                    </x-dashboard.panel>

                    <x-dashboard.panel title="Produk terlaris" class="lg:col-span-2">
                        @foreach ($topProduk as $index => $item)
                            <div class="flex items-center gap-3 py-2 {{ !$loop->last ? $rowClass : '' }}">
                                <x-dashboard.thumb :path="$item->product_image" :alt="$item->product_name" />
                                <div class="flex flex-col min-w-0 flex-1">
                                    <span class="text-sm font-medium truncate">{{ $item->product_name }}</span>
                                    <span class="text-white/40 text-[11px]">{{ $item->total_qty }} terjual</span>
                                </div>
                                <span class="text-xs text-emerald-400 shrink-0">{{ $rupiah($item->total_omzet) }}</span>
                            </div>
                        @endforeach
                    </x-dashboard.panel>
                </div>
            @else
                <x-dashboard.panel title="Penjualan">
                    <x-dashboard.empty icon="bi-graph-up" title="Belum ada penjualan selesai"
                        text="Grafik omzet dan produk terlaris muncul di sini setelah ada pesanan yang berstatus selesai." />
                </x-dashboard.panel>
            @endif

            {{-- 5. Satu baris tenang untuk pengunjung (detailnya di halaman Pengunjung) --}}
            <p class="text-center text-white/35 text-xs">
                {{ $visitsToday }} pengunjung hari ini ·
                <a href="{{ route('dashboard.visitors') }}" class="hover:text-white underline underline-offset-4">Lihat
                    statistik pengunjung</a>
            </p>
        </div>
    </main>
@endsection
