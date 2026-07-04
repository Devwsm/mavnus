<section id="store" class="flex flex-col w-full max-w-7xl mx-auto gap-4 p-6 lg:p-14">

    <!-- ===================== Section Header ===================== -->
    <div class="flex items-center justify-between">
        <h2 class="text-2xl md:text-3xl font-bold uppercase tracking-wide text-[#5E0006]">
            Best Sellers
        </h2>
        <a href="{{ route('home') }}#store"
            class="text-sm font-semibold uppercase tracking-wide underline underline-offset-4 hover:opacity-70 transition">
            Shop All
        </a>
    </div>

    <!-- ===================== Product Grid ===================== -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-2">

        <!-- Product 1 -->
        <a href="{{ route('home') }}#store" class="flex flex-col bg-[#5E0006] gap-4 p-6">
            <div class="w-full overflow-hidden rounded-md">
                <img src="{{ asset('aset/merch/Yalla-Front.png') }}" loading="lazy" decoding="async" alt="Product Name"
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
        <a href="{{ route('home') }}#store" class="flex flex-col bg-[#5E0006] gap-4 p-6">
            <div class="w-full overflow-hidden rounded-md">
                <img src="{{ asset('aset/merch/Yalla-Back.png') }}" loading="lazy" decoding="async" alt="Product Name"
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
        <a href="{{ route('home') }}#store" class="flex flex-col bg-[#5E0006] gap-4 p-6">
            <div class="w-full overflow-hidden rounded-md">
                <img src="{{ asset('aset/merch/Habibi-Front.png') }}" loading="lazy" decoding="async" alt="Product Name"
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
        <a href="{{ route('home') }}#store" class="flex flex-col bg-[#5E0006] gap-4 p-6">
            <div class="w-full overflow-hidden rounded-md">
                <img src="{{ asset('aset/merch/Habibi-Back.png') }}" loading="lazy" decoding="async" alt="Product Name"
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
</section>
