<section class="flex flex-col w-full max-w-7xl gap-20 p-6 lg:p-14">

    <!-- ===================== clothes ===================== -->
    <div class="flex flex-col gap-6">
        <!-- ===================== Section Header ===================== -->
        <div class="flex items-end text-black justify-between border-b border-black/10 pb-4">
            <h1 class="text-2xl md:text-3xl font-bold uppercase tracking-wide">
                Best Sellers
            </h1>
            <a href="{{ route('clothes') }}"
                class="text-sm font-semibold uppercase tracking-wide underline underline-offset-4 decoration-black/30 hover:decoration-black transition">
                Shop All
            </a>
        </div>

        <!-- ===================== Product clothes Grid ===================== -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

            @forelse ($clothesProducts as $product)
                <a href="{{ route('product_detail.clothes', $product) }}"
                    class="group flex flex-col bg-black gap-4 p-5 rounded-2xl overflow-hidden border border-white/5 hover:border-white/15 hover:bg-black/80 transition-all duration-300">
                    <div class="w-full aspect-square overflow-hidden rounded-lg">
                        @if ($product->images->first())
                            <img src="{{ Storage::url($product->images->first()->image_path) }}" loading="lazy"
                                decoding="async" alt="{{ $product->name }}"
                                class="w-full h-full object-cover object-center transition-transform duration-500 group-hover:scale-105">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-white/5">
                                <i class="bi bi-image text-white/20 text-3xl"></i>
                            </div>
                        @endif
                    </div>
                    <div class="flex flex-col text-center gap-1">
                        <h3 class="text-sm font-semibold uppercase tracking-wide text-white transition">
                            {{ $product->name }}
                        </h3>
                        <p class="text-sm text-white/70">{{ $product->formatted_price }}</p>
                    </div>
                </a>
            @empty
                <p class="col-span-full text-center text-black/40 text-sm py-10">
                    Belum ada produk clothes tersedia.
                </p>
            @endforelse

        </div>
    </div>

    <!-- ===================== Accessoris ===================== -->
    <div class="flex flex-col gap-6">
        <!-- ===================== Section Header ===================== -->
        <div class="flex items-end text-black justify-between border-b border-black/10 pb-4">
            <h1 class="text-2xl md:text-3xl font-bold uppercase tracking-wide">
                Accessoris
            </h1>
            <a href="{{ route('accessoris') }}"
                class="text-sm font-semibold uppercase tracking-wide underline underline-offset-4 decoration-black/30 hover:decoration-black transition">
                Shop All
            </a>
        </div>

        <!-- ===================== Product Accessoris ===================== -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

            <!-- Product 1 -->
            <a href="{{ route('accessoris') }}"
                class="group flex flex-col bg-black gap-4 p-5 rounded-2xl overflow-hidden border border-white/5 hover:border-white/15 hover:bg-black/80 transition-all duration-300">
                <div class="w-full aspect-square overflow-hidden rounded-lg">
                    <img src="{{ asset('aset/merch/Habibi-Front.png') }}" loading="lazy" decoding="async" alt="Habibi"
                        class="w-full h-full object-cover object-center transition-transform duration-500 group-hover:scale-105">
                </div>
                <div class="flex flex-col text-center gap-1">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-white transition">
                        Habibi
                    </h3>
                    <p class="text-sm text-white/70">Rp250.000</p>
                </div>
            </a>

            <!-- Product 2 -->
            <a href="{{ route('accessoris') }}"
                class="group flex flex-col bg-black gap-4 p-5 rounded-2xl overflow-hidden border border-white/5 hover:border-white/15 hover:bg-black/80 transition-all duration-300">
                <div class="w-full aspect-square overflow-hidden rounded-lg">
                    <img src="{{ asset('aset/merch/Habibi-Back.png') }}" loading="lazy" decoding="async" alt="Habibi"
                        class="w-full h-full object-cover object-center transition-transform duration-500 group-hover:scale-105">
                </div>
                <div class="flex flex-col text-center gap-1">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-white transition">
                        Habibi
                    </h3>
                    <p class="text-sm text-white/70">Rp250.000</p>
                </div>
            </a>

        </div>
    </div>

    <!-- ===================== Albums ===================== -->
    <div class="flex flex-col gap-6">
        <!-- ===================== Section Header ===================== -->
        <div class="flex items-end text-black justify-between border-b border-black/10 pb-4">
            <h1 class="text-2xl md:text-3xl font-bold uppercase tracking-wide">
                Albums
            </h1>
            <a href="{{ route('albums') }}"
                class="text-sm font-semibold uppercase tracking-wide underline underline-offset-4 decoration-black/30 hover:decoration-black transition">
                Shop All
            </a>
        </div>

        <!-- ===================== Product Albums Grid ===================== -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

            <!-- Product 1 -->
            <a href="{{ route('albums') }}"
                class="group flex flex-col bg-black gap-4 p-5 rounded-2xl overflow-hidden border border-white/5 hover:border-white/15 hover:bg-black/80 transition-all duration-300">
                <div class="w-full aspect-square overflow-hidden rounded-lg">
                    <img src="{{ asset('aset/albums/vamos.png') }}" loading="lazy" decoding="async" alt="Vamos"
                        class="w-full h-full object-cover object-center transition-transform duration-500 group-hover:scale-105">
                </div>
                <div class="flex flex-col text-center gap-1">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-white transition">
                        Vamos
                    </h3>
                    <p class="text-sm text-white/70">Rp250.000</p>
                </div>
            </a>

            <!-- Product 1 -->
            <a href="{{ route('albums') }}"
                class="group flex flex-col bg-black gap-4 p-5 rounded-2xl overflow-hidden border border-white/5 hover:border-white/15 hover:bg-black/80 transition-all duration-300">
                <div class="w-full aspect-square overflow-hidden rounded-lg">
                    <img src="{{ asset('aset/albums/mambo-jambo.png') }}" loading="lazy" decoding="async"
                        alt="mambo jambo"
                        class="w-full h-full object-cover object-center transition-transform duration-500 group-hover:scale-105">
                </div>
                <div class="flex flex-col text-center gap-1">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-white transition">
                        mambo jambo
                    </h3>
                    <p class="text-sm text-white/70">Rp250.000</p>
                </div>
            </a>

            <!-- Product 3 -->
            <a href="{{ route('albums') }}"
                class="group flex flex-col bg-black gap-4 p-5 rounded-2xl overflow-hidden border border-white/5 hover:border-white/15 hover:bg-black/80 transition-all duration-300">
                <div class="w-full aspect-square overflow-hidden rounded-lg">
                    <img src="{{ asset('aset/albums/cartel.png') }}" loading="lazy" decoding="async" alt="cartel"
                        class="w-full h-full object-cover object-center transition-transform duration-500 group-hover:scale-105">
                </div>
                <div class="flex flex-col text-center gap-1">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-white transition">
                        cartel
                    </h3>
                    <p class="text-sm text-white/70">Rp250.000</p>
                </div>
            </a>

            <!-- Product 4 -->
            <a href="{{ route('albums') }}"
                class="group flex flex-col bg-black gap-4 p-5 rounded-2xl overflow-hidden border border-white/5 hover:border-white/15 hover:bg-black/80 transition-all duration-300">
                <div class="w-full aspect-square overflow-hidden rounded-lg">
                    <img src="{{ asset('aset/albums/tequila.png') }}" loading="lazy" decoding="async" alt="tequila"
                        class="w-full h-full object-cover object-center transition-transform duration-500 group-hover:scale-105">
                </div>
                <div class="flex flex-col text-center gap-1">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-white transition">
                        tequila
                    </h3>
                    <p class="text-sm text-white/70">Rp250.000</p>
                </div>
            </a>

        </div>
    </div>
</section>
