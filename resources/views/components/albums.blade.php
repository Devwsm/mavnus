<section class="flex flex-col w-full max-w-7xl gap-24 p-6 lg:p-14 pt-22 md:pt-18 lg:pt-28">

    <!-- ===================== Albums ===================== -->
    <div class="flex flex-col gap-4">
        <!-- ===================== Section Header ===================== -->
        <div class="flex items-center text-black justify-between">
            <h2 class="text-2xl md:text-3xl font-bold uppercase tracking-wide">
                Albums
            </h2>
            <a href="{{ route('albums') }}"
                class="text-sm font-semibold uppercase tracking-wide underline underline-offset-4 hover:opacity-70 transition">
                Shop All
            </a>
        </div>

        <!-- ===================== Product Albums Grid ===================== -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-2">

            <!-- Product 1 -->
            <a href="{{ route('albums') }}" class="flex flex-col bg-black gap-4 p-6">
                <div class="w-full overflow-hidden rounded-full">
                    <img src="{{ asset('aset/albums/vamos.png') }}" loading="lazy" decoding="async"
                        alt="Product Name"
                        class="w-full h-full object-cover object-center transition-transform duration-500">
                </div>
                <div class="flex flex-col text-center gap-2">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-white transition">
                        Product Name
                    </h3>
                    <p class="text-sm text-white">Rp250.000</p>
                </div>
            </a>

            <!-- Product 2 -->
            <a href="{{ route('albums') }}" class="flex flex-col bg-black gap-4 p-6">
                <div class="w-full overflow-hidden rounded-full">
                    <img src="{{ asset('aset/albums/mambo-jambo.png') }}" loading="lazy" decoding="async"
                        alt="Product Name"
                        class="w-full h-full object-cover object-center transition-transform duration-500">
                </div>
                <div class="flex flex-col text-center gap-2">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-white transition">
                        Product Name
                    </h3>
                    <p class="text-sm text-white">Rp250.000</p>
                </div>
            </a>

            <!-- Product 3 -->
            <a href="{{ route('albums') }}" class="flex flex-col bg-black gap-4 p-6">
                <div class="w-full overflow-hidden rounded-full">
                    <img src="{{ asset('aset/albums/cartel.png') }}" loading="lazy" decoding="async"
                        alt="Product Name"
                        class="w-full h-full object-cover object-center transition-transform duration-500">
                </div>
                <div class="flex flex-col text-center gap-2">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-white transition">
                        Product Name
                    </h3>
                    <p class="text-sm text-white">Rp250.000</p>
                </div>
            </a>

            <!-- Product 4 -->
            <a href="{{ route('albums') }}" class="flex flex-col bg-black gap-4 p-6">
                <div class="w-full overflow-hidden rounded-full">
                    <img src="{{ asset('aset/albums/tequila.png') }}" loading="lazy" decoding="async"
                        alt="Product Name"
                        class="w-full h-full object-cover object-center transition-transform duration-500">
                </div>
                <div class="flex flex-col text-center gap-2">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-white transition">
                        Product Name
                    </h3>
                    <p class="text-sm text-white">Rp250.000</p>
                </div>
            </a>

            <!-- Product 5 -->
            <a href="{{ route('albums') }}" class="flex flex-col bg-black gap-4 p-6">
                <div class="w-full overflow-hidden rounded-full">
                    <img src="{{ asset('aset/albums/yalla.png') }}" loading="lazy" decoding="async"
                        alt="Product Name"
                        class="w-full h-full object-cover object-center transition-transform duration-500">
                </div>
                <div class="flex flex-col text-center gap-2">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-white transition">
                        Product Name
                    </h3>
                    <p class="text-sm text-white">Rp250.000</p>
                </div>
            </a>

            <!-- Product 6 -->
            <a href="{{ route('albums') }}" class="flex flex-col bg-black gap-4 p-6">
                <div class="w-full overflow-hidden rounded-full">
                    <img src="{{ asset('aset/albums/be-yours.png') }}" loading="lazy" decoding="async"
                        alt="Product Name"
                        class="w-full h-full object-cover object-center transition-transform duration-500">
                </div>
                <div class="flex flex-col text-center gap-2">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-white transition">
                        Product Name
                    </h3>
                    <p class="text-sm text-white">Rp250.000</p>
                </div>
            </a>

            <!-- Product 7 -->
            <a href="{{ route('albums') }}" class="flex flex-col bg-black gap-4 p-6">
                <div class="w-full overflow-hidden rounded-full">
                    <img src="{{ asset('aset/albums/jTown.png') }}" loading="lazy" decoding="async"
                        alt="Product Name"
                        class="w-full h-full object-cover object-center transition-transform duration-500">
                </div>
                <div class="flex flex-col text-center gap-2">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-white transition">
                        Product Name
                    </h3>
                    <p class="text-sm text-white">Rp250.000</p>
                </div>
            </a>

        </div>
    </div>
</section>
