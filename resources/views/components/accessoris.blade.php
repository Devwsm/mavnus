<section class="flex flex-col w-full max-w-7xl gap-24 p-6 lg:p-14 pt-22 md:pt-18 lg:pt-28">

    <!-- ===================== Accessoris ===================== -->
    <div class="flex flex-col gap-4">
        @include('components/filters')

        <!-- ===================== Product Accessoris ===================== -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

            <!-- Product 1 -->
            <a href="{{ route('product_detail') }}"
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
            <a href="{{ route('product_detail') }}"
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

            <!-- Product 3 -->
            <a href="{{ route('product_detail') }}"
                class="group flex flex-col bg-black gap-4 p-5 rounded-2xl overflow-hidden border border-white/5 hover:border-white/15 hover:bg-black/80 transition-all duration-300">
                <div class="w-full aspect-square overflow-hidden rounded-lg">
                    <img src="{{ asset('aset/merch/Yalla-Front.png') }}" loading="lazy" decoding="async" alt="Yalla"
                        class="w-full h-full object-cover object-center transition-transform duration-500 group-hover:scale-105">
                </div>
                <div class="flex flex-col text-center gap-1">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-white transition">
                        Yalla
                    </h3>
                    <p class="text-sm text-white/70">Rp250.000</p>
                </div>
            </a>

            <!-- Product 4 -->
            <a href="{{ route('product_detail') }}"
                class="group flex flex-col bg-black gap-4 p-5 rounded-2xl overflow-hidden border border-white/5 hover:border-white/15 hover:bg-black/80 transition-all duration-300">
                <div class="w-full aspect-square overflow-hidden rounded-lg">
                    <img src="{{ asset('aset/merch/Yalla-Back.png') }}" loading="lazy" decoding="async" alt="Yalla"
                        class="w-full h-full object-cover object-center transition-transform duration-500 group-hover:scale-105">
                </div>
                <div class="flex flex-col text-center gap-1">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-white transition">
                        Yalla
                    </h3>
                    <p class="text-sm text-white/70">Rp250.000</p>
                </div>
            </a>

        </div>
    </div>

</section>
