{{-- desktop --}}
<div
    class="nav z-50 fixed bottom-5 left-1/2 -translate-x-1/2 hidden lg:flex items-center gap-1 bg-[#0D0D0D] rounded-2xl px-2 py-2">

    <a href="{{ route('dashboard') }}"
        class="group relative flex items-center justify-center w-12 h-12 rounded-xl text-white/85 hover:text-white hover:bg-white/10 text-xl transition {{ request()->routeIs('dashboard') ? 'text-[#B71C1C] bg-[#B71C1C]/20' : '' }}">
        <i class="bi bi-house-door-fill"></i>
        <span
            class="absolute -top-10 left-1/2 -translate-x-1/2 bg-black text-white text-xs font-medium px-2 py-1 rounded-md opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap transition">
            Dashboard
        </span>
    </a>

    <div class="w-px h-6 bg-white/10 mx-1"></div>

    <a href="{{ route('dashboard.orders') }}"
        class="group relative flex items-center justify-center w-12 h-12 rounded-xl text-white/85 hover:text-white hover:bg-white/10 text-xl transition {{ request()->routeIs('dashboard.orders') ? 'text-[#B71C1C] bg-[#B71C1C]/20' : '' }}">
        <i class="bi bi-box-seam"></i>
        <span
            class="absolute -top-10 left-1/2 -translate-x-1/2 bg-black text-white text-xs font-medium px-2 py-1 rounded-md opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap transition">
            Orders
        </span>
    </a>

    {{-- Produk (gabungan Clothes/Accessories/Albums) --}}
    <div class="relative">
        <button type="button" onclick="toggleProductMenu()" id="productMenuBtn"
            class="group relative flex items-center justify-center w-12 h-12 rounded-xl text-white/85 hover:text-white hover:bg-white/10 text-xl transition {{ request()->routeIs(['dashboard.clothes', 'dashboard.accessories', 'dashboard.albums']) ? 'text-[#B71C1C] bg-[#B71C1C]/20' : '' }}">
            <i class="bi bi-grid-fill"></i>
            <span
                class="absolute -top-10 left-1/2 -translate-x-1/2 bg-black text-white text-xs font-medium px-2 py-1 rounded-md opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap transition">
                Produk
            </span>
        </button>

        {{-- Flyout ke atas --}}
        <div id="productMenuFlyout"
            class="hidden absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-44 bg-[#0D0D0D] border border-white/10 rounded-xl p-1.5 flex-col gap-1">
            <a href="{{ route('dashboard.clothes') }}"
                class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm text-white/80 hover:text-white hover:bg-white/10 transition {{ request()->routeIs('dashboard.clothes') ? 'text-[#B71C1C] bg-[#B71C1C]/10' : '' }}">
                <i class="bi bi-bag-fill"></i> Clothes
            </a>
            <a href="{{ route('dashboard') }}"
                class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm text-white/80 hover:text-white hover:bg-white/10 transition {{ request()->routeIs('dashboard.accessories') ? 'text-[#B71C1C] bg-[#B71C1C]/10' : '' }}">
                <i class="bi bi-gem"></i> Accessories
            </a>
            <a href="{{ route('dashboard') }}"
                class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm text-white/80 hover:text-white hover:bg-white/10 transition {{ request()->routeIs('dashboard.albums') ? 'text-[#B71C1C] bg-[#B71C1C]/10' : '' }}">
                <i class="bi bi-disc-fill"></i> Albums
            </a>
        </div>
    </div>

    <div class="w-px h-6 bg-white/10 mx-1"></div>

    <a href="{{ route('dashboard.import-export') }}"
        class="group relative flex items-center justify-center w-12 h-12 rounded-xl text-white/85 hover:text-white hover:bg-white/10 text-xl transition {{ request()->routeIs('dashboard.import-export') ? 'text-[#B71C1C] bg-[#B71C1C]/20' : '' }}">
        <i class="bi bi-file-earmark-excel"></i>
        <span
            class="absolute -top-10 left-1/2 -translate-x-1/2 bg-black text-white text-xs font-medium px-2 py-1 rounded-md opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap transition">
            Import & Export
        </span>
    </a>

    <div class="w-px h-6 bg-white/10 mx-1"></div>

    <a href="{{ route('logout') }}"
        class="group relative flex items-center justify-center w-12 h-12 rounded-xl text-[#B71C1C] hover:text-[#891212] hover:bg-[#B71C1C]/5 text-xl transition">
        <i class="bi bi-box-arrow-right"></i>
        <span
            class="absolute -top-10 left-1/2 -translate-x-1/2 bg-black text-white text-xs font-medium px-2 py-1 rounded-md opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap transition">
            Logout
        </span>
    </a>
</div>
{{-- ↑ DESKTOP DITUTUP DI SINI, sebelum blok mobile dimulai --}}


{{-- Mobile trigger --}}
<button id="dashOpenBtn"
    class="fixed z-80 bottom-5 right-5 lg:hidden flex justify-center items-center h-14 w-14 rounded-full bg-[#1A1A1B] text-[#F5F1E6] text-2xl shadow-lg">
    <i class="bi bi-list"></i>
</button>

{{-- Overlay penutup sisa dashboard --}}
<div id="dashMobileOverlay"
    class="fixed inset-0 bg-black/50 z-80
    opacity-0 pointer-events-none transition-opacity duration-300 lg:hidden">
</div>

{{-- Mobile Fullscreen --}}
<div id="dashMobileMenu"
    class="fixed top-0 right-0 h-full w-1/2 bg-[#0D0D0D] text-white z-80
    flex flex-col items-center justify-center gap-6 border-r-gray-200
    translate-x-full transition-transform duration-300 lg:hidden overflow-y-auto py-10">

    <a href="{{ route('dashboard') }}"
        class="flex flex-col items-center gap-1.5 {{ request()->routeIs('dashboard') ? 'text-[#B71C1C]' : 'text-white' }}">
        <i class="bi bi-house-door-fill text-3xl"></i>
        <span class="text-[10px] font-semibold uppercase tracking-wide">Dashboard</span>
    </a>
    <a href="{{ route('dashboard.orders') }}"
        class="flex flex-col items-center gap-1.5 {{ request()->routeIs('dashboard.orders') ? 'text-[#B71C1C]' : 'text-white' }}">
        <i class="bi bi-box-seam text-3xl"></i>
        <span class="text-[10px] font-semibold uppercase tracking-wide">Orders</span>
    </a>

    {{-- Produk (accordion) --}}
    <div class="flex flex-col items-center gap-3 w-full">
        <button type="button" onclick="toggleMobileProductMenu()"
            class="flex flex-col items-center gap-1.5 {{ request()->routeIs(['dashboard.clothes', 'dashboard.accessories', 'dashboard.albums']) ? 'text-[#B71C1C]' : 'text-white' }}">
            <i class="bi bi-grid-fill text-3xl"></i>
            <span class="text-[10px] font-semibold uppercase tracking-wide flex items-center gap-1">
                Produk <i class="bi bi-chevron-down text-[8px]" id="mobileProductChevron"></i>
            </span>
        </button>

        <div id="mobileProductSubmenu" class="hidden flex-col items-center gap-4 w-full">
            <a href="{{ route('dashboard.clothes') }}"
                class="flex items-center gap-2 text-sm {{ request()->routeIs('dashboard.clothes') ? 'text-[#B71C1C]' : 'text-white/70' }}">
                <i class="bi bi-bag-fill"></i> Clothes
            </a>
            <a href="{{ route('dashboard') }}"
                class="flex items-center gap-2 text-sm {{ request()->routeIs('dashboard.accessories') ? 'text-[#B71C1C]' : 'text-white/70' }}">
                <i class="bi bi-gem"></i> Accessories
            </a>
            <a href="{{ route('dashboard') }}"
                class="flex items-center gap-2 text-sm {{ request()->routeIs('dashboard.albums') ? 'text-[#B71C1C]' : 'text-white/70' }}">
                <i class="bi bi-disc-fill"></i> Albums
            </a>
        </div>
    </div>

    <a href="{{ route('dashboard.import-export') }}"
        class="flex flex-col items-center gap-1.5 {{ request()->routeIs('dashboard.import-export') ? 'text-[#B71C1C]' : 'text-white' }}">
        <i class="bi bi-file-earmark-excel text-3xl"></i>
        <span class="text-[10px] font-semibold uppercase tracking-wide">Import & Export</span>
    </a>
    <a href="{{ route('logout') }}" class="flex flex-col items-center gap-1.5 text-[#B71C1C] hover:text-[#891212]">
        <i class="bi bi-box-arrow-right text-3xl"></i>
        <span class="text-[10px] font-semibold uppercase tracking-wide">Logout</span>
    </a>

    <button id="dashCloseBtn"
        class="fixed bottom-5 right-5 flex justify-center items-center h-14 w-14 rounded-full bg-white/10 border border-white/20 text-white text-3xl">
        <i class="bi bi-x"></i>
    </button>
</div>

<script>
    const dashOpenBtn = document.getElementById('dashOpenBtn');
    const dashCloseBtn = document.getElementById('dashCloseBtn');
    const dashMobileMenu = document.getElementById('dashMobileMenu');
    const dashMobileOverlay = document.getElementById('dashMobileOverlay');

    dashOpenBtn.addEventListener('click', () => {
        dashMobileMenu.classList.remove('translate-x-full');
        dashMobileOverlay.classList.remove('opacity-0', 'pointer-events-none');
        document.body.classList.add('overflow-hidden');
    });

    dashCloseBtn.addEventListener('click', () => {
        dashMobileMenu.classList.add('translate-x-full');
        dashMobileOverlay.classList.add('opacity-0', 'pointer-events-none');
        document.body.classList.remove('overflow-hidden');
    });

    // ---- Desktop: flyout menu Produk ----
    const productMenuBtn = document.getElementById('productMenuBtn');
    const productMenuFlyout = document.getElementById('productMenuFlyout');

    function toggleProductMenu() {
        productMenuFlyout.classList.toggle('hidden');
        productMenuFlyout.classList.toggle('flex');
    }

    document.addEventListener('click', (e) => {
        if (!productMenuFlyout.contains(e.target) && !productMenuBtn.contains(e.target)) {
            productMenuFlyout.classList.add('hidden');
            productMenuFlyout.classList.remove('flex');
        }
    });

    // ---- Mobile: accordion Produk ----
    function toggleMobileProductMenu() {
        const submenu = document.getElementById('mobileProductSubmenu');
        const chevron = document.getElementById('mobileProductChevron');

        submenu.classList.toggle('hidden');
        submenu.classList.toggle('flex');
        chevron.classList.toggle('bi-chevron-down');
        chevron.classList.toggle('bi-chevron-up');
    }
</script>
