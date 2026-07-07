<section class="flex flex-col w-full max-w-7xl gap-24 p-6 lg:p-14 pt-22 md:pt-18 lg:pt-28">

    <!-- ===================== Albums ===================== -->
    <div class="flex flex-col gap-4">
        @include('components/filters')

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

            <!-- Product 2 -->
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
