@extends('template.dashboard.layout')
@section('content')
    <div class="flex flex-col items-center bg-black text-white w-full min-h-screen mb-24">
        @include('components/dashboard/navbar')

        <div class="flex flex-col items-center justify-center p-8 text-center">
            <h1 class="text-3xl font-bold">Mavnus Dashboard</h1>
            <p class="text-white/50 text-sm mt-2 max-w-md">
                Kelola produk clothes, accessories, dan albums dari sini.
            </p>
        </div>

        <div class="flex flex-col w-full max-w-6xl gap-4 p-6 lg:p-14 pb-8">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold uppercase tracking-wide">Pesanan Terbaru</h2>
                <a href="{{ route('dashboard.orders') }}"
                    class="text-white/50 hover:text-white text-sm font-semibold uppercase tracking-wide transition">
                    Lihat Semua →
                </a>
            </div>

            <div class="flex flex-col gap-2">
                @forelse ($recentOrders as $order)
                    @php
                        $statusStyle = match ($order->status) {
                            'pending' => ['bg-[#B77B1C]/10 text-[#B77B1C]', 'bi-hourglass-split'],
                            'processing' => ['bg-[#1C1CB7]/10 text-[#1C1CB7]', 'bi-gear-fill'],
                            'shipped' => ['bg-[#5E1C5E]/10 text-[#5E1C5E]', 'bi-truck'],
                            'completed' => ['bg-[#1C7B1C]/10 text-[#1C7B1C]', 'bi-check-circle-fill'],
                            'cancelled' => ['bg-[#B71C1C]/10 text-[#e05656]', 'bi-x-circle-fill'],
                        };
                    @endphp
                    <a href="{{ route('dashboard.orders.show', $order) }}"
                        class="flex items-center justify-between bg-[#0D0D0D] border border-white/10 hover:border-white/20 rounded-xl px-5 py-4 transition">
                        <div class="flex items-center gap-4">
                            <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg {{ $statusStyle[0] }}">
                                <i class="bi {{ $statusStyle[1] }}"></i>
                            </span>
                            <div class="flex flex-col">
                                <span class="text-sm font-semibold text-white">{{ $order->order_number }}</span>
                                <span class="text-white/40 text-xs">{{ $order->customer_name }} ·
                                    {{ $order->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <span class="text-white font-bold text-sm">Rp{{ number_format($order->total, 0, ',', '.') }}</span>
                    </a>
                @empty
                    <div class="flex flex-col items-center gap-2 py-10 bg-[#0D0D0D] border border-white/10 rounded-xl">
                        <i class="bi bi-receipt text-white/15 text-3xl"></i>
                        <p class="text-white/30 text-sm">Belum ada pesanan masuk.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="flex flex-col w-full max-w-6xl gap-4 p-6 lg:p-14">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold uppercase tracking-wide">Clothes</h2>
                <a href="{{ route('dashboard.clothes') }}"
                    class="bg-[#B71C1C] hover:bg-[#891212] text-white text-sm font-semibold uppercase tracking-wide px-4 py-2 rounded-lg transition">
                    + Tambah Clothes
                </a>
            </div>
            <div class="flex flex-col w-full">
                @include('components/errors/alerts')
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                @forelse ($products as $product)
                    @php
                        $totalStock = $product->variants->sum('stock');
                        $extraImages = $product->images->slice(1, 3);
                        $remainingCount = $product->images->count() - 4;
                    @endphp
                    <div
                        class="group bg-[#0D0D0D] border border-white/10 rounded-2xl overflow-hidden hover:border-white/20 transition-all duration-300">

                        {{-- Foto --}}
                        <div class="relative w-full aspect-square bg-black overflow-hidden">
                            @if ($product->images->first())
                                <img src="{{ Storage::url($product->images->first()->image_path) }}"
                                    alt="{{ $product->name }}"
                                    class="w-full h-full object-cover object-center group-hover:scale-105 transition duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="bi bi-image text-white/15 text-4xl"></i>
                                </div>
                            @endif

                            {{-- Gradient overlay biar strip foto & badge lebih nempel --}}
                            <div
                                class="absolute inset-x-0 bottom-0 h-20 bg-linear-to-t from-black/80 to-transparent pointer-events-none">
                            </div>

                            {{-- Status badge --}}
                            <div class="absolute top-3 right-3">
                                @if ($product->is_active)
                                    <span
                                        class="inline-flex items-center gap-1.5 bg-black/70 backdrop-blur text-green-400 text-[10px] font-semibold px-2 py-1 rounded-md">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-400"></span>
                                        Tersedia
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1.5 bg-black/70 backdrop-blur text-[#e05656] text-[10px] font-semibold px-2 py-1 rounded-md">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#e05656]"></span>
                                        Sold Out
                                    </span>
                                @endif
                            </div>

                            {{-- Strip mini thumbnail (kalau foto lebih dari 1) --}}
                            @if ($product->images->count() > 1)
                                <div class="absolute bottom-3 left-3 flex items-center gap-1.5">
                                    @foreach ($extraImages as $extra)
                                        <div class="w-7 h-7 rounded-md overflow-hidden ring-1.5 ring-white/40">
                                            <img src="{{ Storage::url($extra->image_path) }}" alt="{{ $product->name }}"
                                                class="w-full h-full object-cover object-center">
                                        </div>
                                    @endforeach

                                    @if ($remainingCount > 0)
                                        <div class="w-7 h-7 rounded-md bg-[#B71C1C] flex items-center justify-center">
                                            <span class="text-white text-[10px] font-bold">+{{ $remainingCount }}</span>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="flex flex-col gap-3 p-4">
                            <div>
                                <h3 class="font-semibold text-white uppercase tracking-wide truncate">
                                    {{ $product->name }}
                                </h3>
                                <p class="text-white/40 text-xs mt-0.5 line-clamp-1">
                                    {{ $product->description ?: 'Tidak ada deskripsi' }}
                                </p>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-white font-bold">{{ $product->formatted_price }}</span>
                                <div class="flex items-center gap-1.5">
                                    <span class="w-3 h-3 rounded-full border border-white/20"
                                        style="background-color: {{ $product->clothes->color }}"></span>
                                    <span class="text-white/50 text-xs">{{ $product->clothes->material }}</span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-3 border-t border-white/6">
                                <span class="text-white/40 text-xs">Total Stok</span>
                                <span class="text-white text-sm font-semibold">{{ $totalStock }} pcs</span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-white/40 text-xs">Berat</span>
                                <span class="text-white text-sm">{{ $product->weight }} gram</span>
                            </div>

                            <div class="flex flex-wrap gap-1.5">
                                @foreach ($product->variants as $variant)
                                    @php
                                        $stockColor =
                                            $variant->stock === 0
                                                ? 'text-[#e05656] border-[#B71C1C]/30 bg-[#B71C1C]/5'
                                                : ($variant->stock <= 3
                                                    ? 'text-amber-400 border-amber-400/20 bg-amber-400/5'
                                                    : 'text-green-400 border-green-400/20 bg-green-400/5');
                                    @endphp
                                    <span
                                        class="flex items-center gap-1 border rounded-md px-2 py-1 text-[11px] {{ $stockColor }}">
                                        <span class="font-medium opacity-70">{{ $variant->label }}</span>
                                        <span class="opacity-30">·</span>
                                        <span class="font-semibold">{{ $variant->stock }}</span>
                                    </span>
                                @endforeach
                            </div>

                            {{-- Aksi --}}
                            <div class="flex items-center gap-2 pt-3 border-t border-white/6">
                                @include('components/dashboard/modal-edit-clothes')
                                @include('components/dashboard/btn-hapus-clothes')
                            </div>
                        </div>
                    </div>
                @empty
                    <div
                        class="col-span-full flex flex-col items-center gap-2 py-20 bg-[#0D0D0D] border border-white/10 rounded-xl">
                        <i class="bi bi-inbox text-white/15 text-4xl"></i>
                        <p class="text-white/30 text-sm">Belum ada produk clothes.</p>
                        <p class="text-white/20 text-xs">Klik "Tambah Clothes" untuk mulai menambahkan.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
@endsection
