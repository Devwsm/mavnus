@extends('template.dashboard.layout')
@section('content')
    <main class="flex flex-col items-center bg-black text-white w-full min-h-screen mb-24">
        @include('components/dashboard/navbar')
        @include('components/dashboard/role-header')

        <div class="flex flex-col w-full max-w-4xl gap-6 px-6 lg:px-14 pb-14">
            {{-- Ringkasan utama - lintas semua modul (produk, stok, pesanan, omzet, pengunjung) --}}
            <div class="grid grid-cols-2 lg:grid-cols-3 gap-3">
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
                <div class="bg-[#0D0D0D] border border-white/10 rounded-2xl p-4 flex flex-col gap-1">
                    <span class="text-white/40 text-[11px] uppercase tracking-wide">Pesanan Pending</span>
                    <span class="text-2xl font-bold {{ $pesananPendingCount > 0 ? 'text-[#e05656]' : '' }}">
                        {{ $pesananPendingCount }}
                    </span>
                </div>
                <div class="bg-[#0D0D0D] border border-white/10 rounded-2xl p-4 flex flex-col gap-1">
                    <span class="text-white/40 text-[11px] uppercase tracking-wide">Omzet Bulan Ini</span>
                    <span class="text-xl lg:text-2xl font-bold text-green-400">
                        Rp{{ number_format($omzetBulanIni, 0, ',', '.') }}
                    </span>
                </div>
                <div class="bg-[#0D0D0D] border border-white/10 rounded-2xl p-4 flex flex-col gap-1">
                    <span class="text-white/40 text-[11px] uppercase tracking-wide">Pengunjung Hari Ini</span>
                    <span class="text-2xl font-bold">{{ $visitsToday }}</span>
                    <span class="text-white/30 text-[10px]">{{ $visitsThisWeek }} kunjungan 7 hari terakhir</span>
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

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
                {{-- Ringkasan pesanan per status --}}
                <div class="lg:col-span-2 bg-[#0D0D0D] border border-white/10 rounded-2xl p-5 flex flex-col gap-4">
                    <h2 class="text-white/40 text-[11px] font-semibold uppercase tracking-widest">
                        Status Pesanan
                    </h2>
                    <div class="flex flex-col gap-2.5">
                        @php
                            $statusLabels = [
                                'pending' => ['label' => 'Pending', 'dot' => 'bg-[#B77B1C]'],
                                'processing' => ['label' => 'Diproses', 'dot' => 'bg-[#1C1CB7]'],
                                'shipped' => ['label' => 'Dikirim', 'dot' => 'bg-[#5E1C5E]'],
                                'completed' => ['label' => 'Selesai', 'dot' => 'bg-[#1C7B1C]'],
                                'cancelled' => ['label' => 'Dibatalkan', 'dot' => 'bg-white/30'],
                            ];
                        @endphp
                        @foreach ($statusLabels as $key => $meta)
                            <div class="flex items-center justify-between text-sm">
                                <span class="flex items-center gap-2 text-white/60">
                                    <span class="w-2 h-2 rounded-full {{ $meta['dot'] }}"></span>
                                    {{ $meta['label'] }}
                                </span>
                                <span class="font-semibold">{{ $orderStatusCounts[$key] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Preview stok paling kritis --}}
                <div class="lg:col-span-3 bg-[#0D0D0D] border border-white/10 rounded-2xl p-5 flex flex-col gap-3">
                    <div class="flex items-center justify-between">
                        <h2 class="text-white/40 text-[11px] font-semibold uppercase tracking-widest">
                            Stok Perlu Perhatian
                        </h2>
                        <a href="{{ route('dashboard.produk') }}"
                            class="text-white/40 hover:text-white text-xs transition">Kelola produk</a>
                    </div>

                    @forelse ($criticalVariants as $variant)
                        <div class="flex items-center gap-3 py-2 {{ !$loop->last ? 'border-b border-white/6' : '' }}">
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
                        </div>
                    @empty
                        <p class="text-white/30 text-sm py-4 text-center">Semua stok aman, gak ada yang perlu buru-buru.</p>
                    @endforelse
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
                {{-- Omzet 6 bulan terakhir --}}
                <div class="lg:col-span-3 bg-[#0D0D0D] border border-white/10 rounded-2xl p-5 flex flex-col gap-4">
                    <h2 class="text-white/40 text-[11px] font-semibold uppercase tracking-widest">
                        Omzet 6 Bulan Terakhir
                    </h2>
                    <div class="flex items-end justify-between gap-2 h-28">
                        @foreach ($omzetPerBulan as $bulan)
                            @php $heightPct = max(4, ($bulan['total'] / $omzetPerBulanMax) * 100); @endphp
                            <div class="flex-1 flex flex-col items-center gap-1.5 h-full justify-end">
                                <span class="text-white/50 text-[10px] font-semibold text-center leading-tight">
                                    {{ $bulan['total'] > 0 ? 'Rp' . number_format($bulan['total'] / 1000, 0, ',', '.') . 'rb' : '-' }}
                                </span>
                                <div class="w-full rounded-t-md {{ $bulan['total'] > 0 ? 'bg-green-500' : 'bg-white/10' }}"
                                    style="height: {{ $heightPct }}%"></div>
                                <span class="text-white/30 text-[10px]">{{ $bulan['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Omzet per kategori bulan ini --}}
                <div class="lg:col-span-2 bg-[#0D0D0D] border border-white/10 rounded-2xl p-5 flex flex-col gap-4">
                    <h2 class="text-white/40 text-[11px] font-semibold uppercase tracking-widest">
                        Omzet per Kategori (Bulan Ini)
                    </h2>
                    @if ($omzetPerKategoriTotal > 1 || $omzetPerKategori['clothes'] > 0 || $omzetPerKategori['accessories'] > 0)
                        <div class="w-full h-3 rounded-full overflow-hidden flex bg-white/5">
                            <div class="h-full bg-[#B71C1C]"
                                style="width: {{ ($omzetPerKategori['clothes'] / $omzetPerKategoriTotal) * 100 }}%"></div>
                            <div class="h-full bg-amber-400"
                                style="width: {{ ($omzetPerKategori['accessories'] / $omzetPerKategoriTotal) * 100 }}%">
                            </div>
                        </div>
                        <div class="flex flex-col gap-2.5">
                            <div class="flex items-center justify-between text-sm">
                                <span class="flex items-center gap-2 text-white/60">
                                    <span class="w-2 h-2 rounded-full bg-[#B71C1C]"></span>
                                    Clothes
                                </span>
                                <span
                                    class="font-semibold">Rp{{ number_format($omzetPerKategori['clothes'], 0, ',', '.') }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="flex items-center gap-2 text-white/60">
                                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                                    Accessories
                                </span>
                                <span
                                    class="font-semibold">Rp{{ number_format($omzetPerKategori['accessories'], 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @else
                        <p class="text-white/30 text-sm py-4 text-center">Belum ada pesanan selesai bulan ini.</p>
                    @endif
                </div>
            </div>

            {{-- Produk terlaris --}}
            <div class="bg-[#0D0D0D] border border-white/10 rounded-2xl p-5 flex flex-col gap-3">
                <div class="flex items-center justify-between">
                    <h2 class="text-white/40 text-[11px] font-semibold uppercase tracking-widest">
                        Produk Terlaris (Sepanjang Waktu)
                    </h2>
                    <a href="{{ route('dashboard.produk') }}"
                        class="text-white/40 hover:text-white text-xs transition">Kelola produk</a>
                </div>

                @forelse ($topProduk as $index => $item)
                    <div class="flex items-center gap-3 py-2.5 {{ !$loop->last ? 'border-b border-white/6' : '' }}">
                        <span class="text-white/30 text-xs font-semibold w-4 shrink-0">{{ $index + 1 }}</span>
                        <div class="w-9 h-9 rounded-lg bg-white/5 overflow-hidden shrink-0">
                            @if ($item->product_image)
                                <img src="{{ Storage::url($item->product_image) }}" alt="{{ $item->product_name }}"
                                    class="w-full h-full object-cover">
                            @endif
                        </div>
                        <div class="flex flex-col min-w-0 flex-1">
                            <span class="text-sm font-semibold truncate">{{ $item->product_name }}</span>
                            <span class="text-white/40 text-[11px]">{{ $item->total_qty }} pcs terjual</span>
                        </div>
                        <span class="text-xs font-semibold text-green-400 shrink-0">
                            Rp{{ number_format($item->total_omzet, 0, ',', '.') }}
                        </span>
                    </div>
                @empty
                    <p class="text-white/30 text-sm py-4 text-center">Belum ada pesanan selesai buat dihitung.</p>
                @endforelse
            </div>

            {{-- Ringkasan pengunjung --}}
            <div class="bg-[#0D0D0D] border border-white/10 rounded-2xl p-5 flex flex-col gap-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-white/40 text-[11px] font-semibold uppercase tracking-widest">
                        Perangkat Pengunjung
                    </h2>
                    <a href="{{ route('dashboard.visitors') }}"
                        class="text-white/40 hover:text-white text-xs transition">Detail statistik</a>
                </div>

                @if ($totalDeviceVisits > 0 && $deviceStats->sum() > 0)
                    @php
                        $deviceLabels = [
                            'desktop' => 'Desktop',
                            'mobile' => 'Mobile',
                            'tablet' => 'Tablet',
                            'bot' => 'Bot',
                            'unknown' => 'Lainnya',
                        ];
                        $deviceColors = [
                            'desktop' => 'bg-[#B71C1C]',
                            'mobile' => 'bg-amber-400',
                            'tablet' => 'bg-[#5E1C5E]',
                            'bot' => 'bg-white/20',
                            'unknown' => 'bg-white/10',
                        ];
                    @endphp
                    <div class="w-full h-3 rounded-full overflow-hidden flex bg-white/5">
                        @foreach ($deviceStats as $device => $total)
                            <div class="h-full {{ $deviceColors[$device] ?? 'bg-white/10' }}"
                                style="width: {{ ($total / $totalDeviceVisits) * 100 }}%"></div>
                        @endforeach
                    </div>
                    <div class="flex flex-wrap gap-x-5 gap-y-2 text-xs">
                        @foreach ($deviceStats as $device => $total)
                            <span class="flex items-center gap-1.5 text-white/60">
                                <span class="w-2 h-2 rounded-full {{ $deviceColors[$device] ?? 'bg-white/10' }}"></span>
                                {{ $deviceLabels[$device] ?? ucfirst($device) }} ({{ $total }})
                            </span>
                        @endforeach
                    </div>
                @else
                    <p class="text-white/30 text-sm">Belum ada data kunjungan.</p>
                @endif
            </div>
        </div>
    </main>
@endsection
