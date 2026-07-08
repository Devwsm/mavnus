<div class="flex items-center justify-between border-b border-black/10 pb-4">
    <div class="relative flex items-center gap-4">
        <h1 class="text-sm font-semibold uppercase tracking-wide">
            Filter:
        </h1>

        <button type="button" onclick="togglePriceFilter()"
            class="flex items-center gap-1.5 text-sm font-semibold uppercase underline tracking-wide">
            Price
            <i class="bi bi-chevron-down text-xs"></i>
        </button>

        {{-- Dropdown (tablet & desktop) --}}
        <div id="priceFilterDropdown"
            class="hidden absolute top-full left-0 mt-2 w-72 bg-white border border-black/10 rounded-lg shadow-lg p-4 z-20">
            @include('components/price-filter-form')
        </div>
    </div>

    <div class="flex">
        <h1 class="text-sm font-semibold uppercase tracking-wide">
            {{ $products->total() }} products
        </h1>
    </div>
</div>

{{-- Backdrop (mobile only) --}}
<div id="priceFilterBackdrop"
    class="hidden md:hidden! fixed inset-0 bg-black/50 z-70 opacity-0 pointer-events-none transition-opacity duration-300">
</div>

{{-- Drawer dari kiri (mobile only) --}}
<div id="priceFilterDrawer"
    class="fixed top-0 left-0 h-full w-3/4 bg-white text-black z-80
    flex flex-col -translate-x-full transition-transform duration-300 md:hidden">

    <div class="flex items-center justify-between p-6 border-b border-black/10">
        <h2 class="text-lg font-bold uppercase tracking-wide">Filter Harga</h2>
        <button type="button" onclick="closePriceFilterMobile()" class="text-2xl">
            <i class="bi bi-x"></i>
        </button>
    </div>

    <div class="p-6">
        @include('components/price-filter-form')
    </div>
</div>

@once
    <script>
        const priceFilterDropdown = document.getElementById('priceFilterDropdown');
        const priceFilterDrawer = document.getElementById('priceFilterDrawer');
        const priceFilterBackdrop = document.getElementById('priceFilterBackdrop');

        function togglePriceFilter() {
            if (window.innerWidth < 768) {
                openPriceFilterMobile();
            } else {
                priceFilterDropdown.classList.toggle('hidden');
            }
        }

        function openPriceFilterMobile() {
            priceFilterDrawer.classList.remove('-translate-x-full');
            priceFilterBackdrop.classList.remove('hidden', 'opacity-0', 'pointer-events-none');
            document.body.classList.add('overflow-hidden');
        }

        function closePriceFilterMobile() {
            priceFilterDrawer.classList.add('-translate-x-full');
            priceFilterBackdrop.classList.add('opacity-0', 'pointer-events-none');
            document.body.classList.remove('overflow-hidden');
            setTimeout(() => priceFilterBackdrop.classList.add('hidden'), 300);
        }

        priceFilterBackdrop.addEventListener('click', closePriceFilterMobile);

        document.addEventListener('click', (e) => {
            if (window.innerWidth >= 768) {
                const isToggleBtn = e.target.closest('button')?.getAttribute('onclick') === 'togglePriceFilter()';
                if (!priceFilterDropdown.contains(e.target) && !isToggleBtn) {
                    priceFilterDropdown.classList.add('hidden');
                }
            }
        });
    </script>
@endonce
