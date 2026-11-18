@extends('template.dashboard.layout')
@section('content')
    @php
        $rupiah = fn($amount) => 'Rp' . number_format($amount, 0, ',', '.');

        // Tampilan bar perangkat pengunjung (satu-satunya tempat label & warna perangkat diatur di dashboard)
        $deviceMeta = [
            'desktop' => ['Desktop', 'bg-[#B71C1C]'],
            'mobile' => ['Mobile', 'bg-amber-400'],
            'tablet' => ['Tablet', 'bg-[#5E1C5E]'],
            'bot' => ['Bot', 'bg-white/20'],
            'unknown' => ['Lainnya', 'bg-white/10'],
        ];
        $deviceSegments = $deviceStats
            ->map(
                fn($total, $device) => [
                    'label' => $deviceMeta[$device][0] ?? ucfirst($device),
                    'value' => $total,
                    'color' => $deviceMeta[$device][1] ?? 'bg-white/10',
                ],
            )
            ->values();

        $categorySegments = [
            [
                'label' => 'Clothes',
                'value' => $omzetPerKategori['clothes'],
                'color' => 'bg-[#B71C1C]',
                'text' => $rupiah($omzetPerKategori['clothes']),
            ],
            [
                'label' => 'Accessories',
                'value' => $omzetPerKategori['accessories'],
                'color' => 'bg-amber-400',
                'text' => $rupiah($omzetPerKategori['accessories']),
            ],
        ];

        $rowClass = 'border-b border-white/6';
    @endphp

    <main class="flex flex-col items-center bg-black text-white w-full min-h-screen mb-24">
        @include('components/dashboard/navbar')

        <div class="flex flex-col items-center gap-2.5 p-8 text-center">
            <h1 class="text-2xl md:text-3xl font-bold">Halo, {{ session('name') ?: session('user') }}</h1>
        </div>

        <div class="flex flex-col w-full max-w-5xl gap-6 px-6 lg:px-14 pb-14">
            {{-- Ringkasan utama: produk, stok, pesanan, omzet, pengunjung, anggota --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                <x-dashboard.stat-card label="Produk Aktif" :value="$totalProdukAktif" />
                <x-dashboard.stat-card label="Rilis Terjadwal" :value="$scheduledCount" />
                <x-dashboard.stat-card label="Stok Menipis" :value="$lowStockCount" :value-class="$lowStockCount > 0 ? 'text-amber-400' : ''" />
                <x-dashboard.stat-card label="Stok Habis" :value="$outOfStockCount" :value-class="$outOfStockCount > 0 ? 'text-[#e05656]' : ''" />
                <x-dashboard.stat-card label="Pesanan Pending" :value="$orderStatusCounts['pending']" :value-class="$orderStatusCounts['pending'] > 0 ? 'text-[#e05656]' : ''" />
                <x-dashboard.stat-card label="Omzet Bulan Ini" :value="$rupiah($omzetBulanIni)" value-class="text-green-400" />
                <x-dashboard.stat-card label="Pengunjung Hari Ini" :value="$visitsToday" :note="$visitsThisWeek . ' kunjungan 7 hari terakhir'" />
                <x-dashboard.stat-card label="Anggota" :value="$totalMembers" :note="$newMembersThisMonth . ' anggota baru bulan ini'" />
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
                <x-dashboard.panel title="Pesanan 7 Hari Terakhir" class="lg:col-span-2">
                    <x-dashboard.bar-chart :items="$ordersTrend->map(
                        fn($day) => ['label' => $day['label'], 'value' => $day['total'], 'text' => $day['total']],
                    )" :max="$ordersTrendMax" />
                </x-dashboard.panel>

                <x-dashboard.panel title="Pesanan Terbaru" :href="route('dashboard.orders')" link="Lihat semua"
                    class="lg:col-span-3 gap-3">
                    @forelse ($recentOrders as $order)
                        <a href="{{ route('dashboard.orders.show', $order) }}"
                            class="flex items-center justify-between gap-3 py-2.5 {{ !$loop->last ? $rowClass : '' }} hover:bg-white/5 -mx-2 px-2 rounded-lg transition">
                            <div class="flex flex-col min-w-0">
                                <span class="text-sm font-semibold truncate">{{ $order->customer_name }}</span>
                                <span class="text-white/30 text-[11px]">{{ $order->order_number }}</span>
                            </div>
                            <div class="flex items-center gap-2.5 shrink-0">
                                <span class="text-xs font-semibold">{{ $rupiah($order->total) }}</span>
                                <x-dashboard.status-badge :order="$order" class="rounded-md px-2" />
                            </div>
                        </a>
                    @empty
                        <p class="text-white/30 text-sm py-4 text-center">Belum ada pesanan masuk.</p>
                    @endforelse
                </x-dashboard.panel>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
                <x-dashboard.panel title="Status Pesanan" class="lg:col-span-2">
                    <div class="flex flex-col gap-2.5">
                        @foreach (\App\Models\Order::STATUS_META as $status => $meta)
                            <div class="flex items-center justify-between text-sm">
                                <span class="flex items-center gap-2 text-white/60">
                                    <span class="w-2 h-2 rounded-full"
                                        style="background-color: {{ $meta['color'] }}"></span>
                                    {{ $meta['label'] }}
                                </span>
                                <span class="font-semibold">{{ $orderStatusCounts[$status] }}</span>
                            </div>
                        @endforeach
                    </div>
                </x-dashboard.panel>

                <x-dashboard.panel title="Stok Perlu Perhatian" :href="route('dashboard.produk')" link="Kelola produk"
                    class="lg:col-span-3 gap-3">
                    @forelse ($criticalItems as $item)
                        @php $productId = $item['product']?->id_product; @endphp
                        <a href="{{ $productId ? route('dashboard.produk', ['edit' => $productId]) . '#product-' . $productId : route('dashboard.produk') }}"
                            class="flex items-center gap-3 py-2 {{ !$loop->last ? $rowClass : '' }} hover:bg-white/5 -mx-2 px-2 rounded-lg transition">
                            <x-dashboard.thumb :path="$item['product']?->images->first()?->image_path" :alt="$item['product']?->name" />
                            <div class="flex flex-col min-w-0 flex-1">
                                <span class="text-sm font-semibold truncate">
                                    {{ $item['product']?->name ?? 'Produk tidak ditemukan' }}
                                </span>
                                <span class="text-white/40 text-[11px]">{{ $item['label'] }}</span>
                            </div>
                            <span
                                class="text-[11px] font-semibold px-2 py-1 rounded-md shrink-0 {{ $item['stock'] === 0 ? 'text-[#e05656] bg-[#B71C1C]/10' : 'text-amber-400 bg-amber-400/10' }}">
                                {{ $item['stock'] === 0 ? 'Habis' : $item['stock'] . ' pcs' }}
                            </span>
                        </a>
                    @empty
                        <p class="text-white/30 text-sm py-4 text-center">Semua stok aman, gak ada yang perlu buru-buru.</p>
                    @endforelse
                </x-dashboard.panel>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
                <x-dashboard.panel title="Omzet 6 Bulan Terakhir" class="lg:col-span-3">
                    <x-dashboard.bar-chart color="bg-green-500" :max="$omzetPerBulanMax" :items="$omzetPerBulan->map(
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

                <x-dashboard.panel title="Omzet per Kategori (Bulan Ini)" class="lg:col-span-2">
                    @if (array_sum($omzetPerKategori) > 0)
                        <x-dashboard.stack-bar :segments="$categorySegments" :total="$omzetPerKategoriTotal" />
                    @else
                        <p class="text-white/30 text-sm py-4 text-center">Belum ada pesanan selesai bulan ini.</p>
                    @endif
                </x-dashboard.panel>
            </div>

            <x-dashboard.panel title="Produk Terlaris (Sepanjang Waktu)" :href="route('dashboard.produk')" link="Kelola produk"
                class="gap-3">
                @forelse ($topProduk as $index => $item)
                    <div class="flex items-center gap-3 py-2.5 {{ !$loop->last ? $rowClass : '' }}">
                        <span class="text-white/30 text-xs font-semibold w-4 shrink-0">{{ $index + 1 }}</span>
                        <x-dashboard.thumb :path="$item->product_image" :alt="$item->product_name" />
                        <div class="flex flex-col min-w-0 flex-1">
                            <span class="text-sm font-semibold truncate">{{ $item->product_name }}</span>
                            <span class="text-white/40 text-[11px]">{{ $item->total_qty }} pcs terjual</span>
                        </div>
                        <span
                            class="text-xs font-semibold text-green-400 shrink-0">{{ $rupiah($item->total_omzet) }}</span>
                    </div>
                @empty
                    <p class="text-white/30 text-sm py-4 text-center">Belum ada pesanan selesai buat dihitung.</p>
                @endforelse
            </x-dashboard.panel>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
                <x-dashboard.panel title="Anggota Terbaru" :href="route('dashboard.members')" link="Lihat semua"
                    class="lg:col-span-3 gap-3">
                    @forelse ($recentMembers as $member)
                        <a href="{{ route('dashboard.members.show', $member) }}"
                            class="flex items-center gap-3 py-2.5 {{ !$loop->last ? $rowClass : '' }} hover:bg-white/5 -mx-2 px-2 rounded-lg transition">
                            <x-dashboard.avatar :name="$member->name" />
                            <div class="flex flex-col min-w-0 flex-1">
                                <span class="text-sm font-semibold truncate">{{ $member->name }}</span>
                                <span class="text-white/40 text-[11px] truncate">{{ $member->email }}</span>
                            </div>
                            <div class="flex flex-col items-end shrink-0">
                                <span class="text-xs font-semibold">{{ $member->orders_count }} pesanan</span>
                                <span class="text-white/30 text-[10px]">{{ $member->created_at->diffForHumans() }}</span>
                            </div>
                        </a>
                    @empty
                        <p class="text-white/30 text-sm py-4 text-center">Belum ada anggota yang mendaftar.</p>
                    @endforelse
                </x-dashboard.panel>

                <x-dashboard.panel title="Perangkat Pengunjung" :href="route('dashboard.visitors')" link="Detail statistik"
                    class="lg:col-span-2">
                    @if ($deviceStats->sum() > 0)
                        <x-dashboard.stack-bar :segments="$deviceSegments" :total="$totalDeviceVisits" />
                    @else
                        <p class="text-white/30 text-sm">Belum ada data kunjungan.</p>
                    @endif
                </x-dashboard.panel>
            </div>
        </div>
    </main>
@endsection
