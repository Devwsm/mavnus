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

        <div class="flex flex-col w-full max-w-6xl gap-4 p-6 lg:p-14">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold uppercase tracking-wide">Clothes</h2>
                <a href="{{ route('dashboard.clothes') }}"
                    class="bg-[#B71C1C] hover:bg-[#891212] text-white text-sm font-semibold uppercase tracking-wide px-4 py-2 rounded-lg transition">
                    + Tambah Clothes
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                @forelse ($products as $product)
                    @php
                        $totalStock = $product->clothes->variants->sum('stock');
                    @endphp
                    <div class="bg-[#0D0D0D] border border-white/10 rounded-2xl overflow-hidden 
                            hover:border-white/20 transition">
                        {{-- Foto --}}
                        <div class="relative w-full aspect-square bg-black overflow-hidden">
                            @if ($product->images->first())
                                <img src="{{ Storage::url($product->images->first()->image_path) }}"
                                    alt="{{ $product->name }}" class="w-full h-full object-cover object-center">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="bi bi-image text-white/15 text-4xl"></i>
                                </div>
                            @endif
                            @if ($product->images->count() > 1)
                                <span
                                    class="absolute top-3 left-3 bg-black/70 backdrop-blur text-white/90 text-[10px] font-bold px-2 py-1 rounded-md">
                                    {{ $product->images->count() }} foto
                                </span>
                            @endif
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
                            <div class="flex flex-wrap gap-1.5">
                                @foreach ($product->clothes->variants as $variant)
                                    <span
                                        class="flex items-center gap-1 bg-black border border-white/10 rounded-md px-2 py-1 text-[11px]">
                                        <span class="text-white/50 font-medium">{{ $variant->size }}</span>
                                        <span class="text-white/25">·</span>
                                        <span class="{{ $variant->stock === 0 ? 'text-[#B71C1C]' : 'text-white/70' }}">
                                            {{ $variant->stock }}
                                        </span>
                                    </span>
                                @endforeach
                            </div>

                            {{-- Aksi --}}
                            <div class="flex items-center gap-2 pt-3 border-t border-white/6">
                                <a href="#"
                                    class="flex-1 flex items-center justify-center gap-1.5 bg-white/5 hover:bg-white/10 text-white text-xs font-semibold py-2 rounded-lg transition">
                                    <i class="bi bi-pencil-square"></i>
                                    Edit
                                </a>
                                @include('components/dashboard/btn-hapus-clothes')
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full flex flex-col items-center gap-2 py-20">
                        <i class="bi bi-inbox text-white/15 text-4xl"></i>
                        <p class="text-white/30 text-sm">Belum ada produk clothes.</p>
                        <p class="text-white/20 text-xs">Klik "Tambah Clothes" untuk mulai menambahkan.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
@endsection
