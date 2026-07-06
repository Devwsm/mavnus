<section class="flex flex-col w-full max-w-7xl gap-24 p-6 lg:p-14 pt-22 md:pt-18 lg:pt-28">

    <!-- ===================== Albums ===================== -->
    <div class="flex flex-col gap-4">
        @include('components/filters')

        <!-- ===================== Product Albums Grid ===================== -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-2">

            <!-- Product 1 -->
            <a href="{{ route('albums') }}" class="flex flex-col bg-black gap-4 p-6">
                <div class="w-full overflow-hidden rounded-full">
                    <img src="{{ asset('aset/albums/vamos.png') }}" loading="lazy" decoding="async"
                        alt="Vamos"
                        class="w-full h-full object-cover object-center transition-transform duration-500">
                </div>
                <div class="flex flex-col text-center gap-2">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-white transition">
                        Vamos
                    </h3>
                    <p class="text-sm text-white">Rp250.000</p>
                </div>
            </a>

            <!-- Product 2 -->
            <a href="{{ route('albums') }}" class="flex flex-col bg-black gap-4 p-6">
                <div class="w-full overflow-hidden rounded-full">
                    <img src="{{ asset('aset/albums/mambo-jambo.png') }}" loading="lazy" decoding="async"
                        alt="mambo jambo"
                        class="w-full h-full object-cover object-center transition-transform duration-500">
                </div>
                <div class="flex flex-col text-center gap-2">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-white transition">
                        mambo jambo
                    </h3>
                    <p class="text-sm text-white">Rp250.000</p>
                </div>
            </a>

            <!-- Product 3 -->
            <a href="{{ route('albums') }}" class="flex flex-col bg-black gap-4 p-6">
                <div class="w-full overflow-hidden rounded-full">
                    <img src="{{ asset('aset/albums/cartel.png') }}" loading="lazy" decoding="async"
                        alt="cartel"
                        class="w-full h-full object-cover object-center transition-transform duration-500">
                </div>
                <div class="flex flex-col text-center gap-2">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-white transition">
                        cartel
                    </h3>
                    <p class="text-sm text-white">Rp250.000</p>
                </div>
            </a>

            <!-- Product 4 -->
            <a href="{{ route('albums') }}" class="flex flex-col bg-black gap-4 p-6">
                <div class="w-full overflow-hidden rounded-full">
                    <img src="{{ asset('aset/albums/tequila.png') }}" loading="lazy" decoding="async"
                        alt="tequila"
                        class="w-full h-full object-cover object-center transition-transform duration-500">
                </div>
                <div class="flex flex-col text-center gap-2">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-white transition">
                        tequila
                    </h3>
                    <p class="text-sm text-white">Rp250.000</p>
                </div>
            </a>

            <!-- Product 5 -->
            <a href="{{ route('albums') }}" class="flex flex-col bg-black gap-4 p-6">
                <div class="w-full overflow-hidden rounded-full">
                    <img src="{{ asset('aset/albums/yalla.png') }}" loading="lazy" decoding="async"
                        alt="yalla"
                        class="w-full h-full object-cover object-center transition-transform duration-500">
                </div>
                <div class="flex flex-col text-center gap-2">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-white transition">
                        yalla
                    </h3>
                    <p class="text-sm text-white">Rp250.000</p>
                </div>
            </a>

            <!-- Product 6 -->
            <a href="{{ route('albums') }}" class="flex flex-col bg-black gap-4 p-6">
                <div class="w-full overflow-hidden rounded-full">
                    <img src="{{ asset('aset/albums/be-yours.png') }}" loading="lazy" decoding="async"
                        alt="i`l be yours"
                        class="w-full h-full object-cover object-center transition-transform duration-500">
                </div>
                <div class="flex flex-col text-center gap-2">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-white transition">
                        i`l be yours
                    </h3>
                    <p class="text-sm text-white">Rp250.000</p>
                </div>
            </a>

            <!-- Product 7 -->
            <a href="{{ route('albums') }}" class="flex flex-col bg-black gap-4 p-6">
                <div class="w-full overflow-hidden rounded-full">
                    <img src="{{ asset('aset/albums/jTown.png') }}" loading="lazy" decoding="async"
                        alt="jTown"
                        class="w-full h-full object-cover object-center transition-transform duration-500">
                </div>
                <div class="flex flex-col text-center gap-2">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-white transition">
                        jTown
                    </h3>
                    <p class="text-sm text-white">Rp250.000</p>
                </div>
            </a>

        </div>
    </div>
</section>
