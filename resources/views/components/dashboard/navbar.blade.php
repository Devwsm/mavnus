{{--
    Navbar dashboard (desktop pill bawah + mobile fullscreen). Menu yang
    ditampilin disaring sesuai role staff yang login (session 'role'),
    biar gak ada menu yang keliatan padahal bakal ditolak middleware
    kalau diklik. Urutan & style dipertahankan sama kayak sebelumnya.
--}}
@php
    $role = session('role');
    $canOrders = in_array($role, ['owner', 'staff_pesanan']);
    $canVisitors = $role === 'owner';
    $canProduk = in_array($role, ['owner', 'admin_produk']);

    // Badge stok menipis cuma relevan buat role yang urus stok
    $navLowStockCount = 0;
    if (in_array($role, ['owner', 'admin_produk'])) {
        $navLowStockCount = \App\Models\ProductVariant::where('stock', '>', 0)
            ->where('stock', '<=', 3)
            ->whereHas('product', fn($query) => $query->where('is_active', true))
            ->count();
    }
@endphp
{{-- desktop --}}
<div
    class="nav z-50 fixed bottom-5 left-1/2 -translate-x-1/2 hidden lg:flex items-center gap-1 bg-[#0D0D0D] rounded-2xl px-2 py-2">

    <a href="{{ route('dashboard') }}"
        class="group relative flex items-center justify-center w-12 h-12 rounded-xl text-white/85 hover:text-white hover:bg-white/10 text-xl transition {{ request()->routeIs('dashboard') ? 'text-[#B71C1C] bg-[#B71C1C]/20' : '' }}">
        <i class="bi bi-house-door-fill"></i>
        @if ($navLowStockCount > 0)
            <span
                class="absolute top-1.5 right-1.5 flex items-center justify-center min-w-4 h-4 px-1 rounded-full bg-amber-400 text-black text-[9px] font-bold">
                {{ $navLowStockCount > 9 ? '9+' : $navLowStockCount }}
            </span>
        @endif
        <span
            class="absolute -top-10 left-1/2 -translate-x-1/2 bg-black text-white text-xs font-medium px-2 py-1 rounded-md opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap transition">
            Dashboard
        </span>
    </a>

    @if ($canOrders)
        <div class="w-px h-6 bg-white/10 mx-1"></div>

        <a href="{{ route('dashboard.orders') }}"
            class="group relative flex items-center justify-center w-12 h-12 rounded-xl text-white/85 hover:text-white hover:bg-white/10 text-xl transition {{ request()->routeIs('dashboard.orders') ? 'text-[#B71C1C] bg-[#B71C1C]/20' : '' }}">
            <i class="bi bi-box-seam"></i>
            <span
                class="absolute -top-10 left-1/2 -translate-x-1/2 bg-black text-white text-xs font-medium px-2 py-1 rounded-md opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap transition">
                Orders
            </span>
        </a>
    @endif

    @if ($canVisitors)
        <div class="w-px h-6 bg-white/10 mx-1"></div>

        <a href="{{ route('dashboard.visitors') }}"
            class="group relative flex items-center justify-center w-12 h-12 rounded-xl text-white/85 hover:text-white hover:bg-white/10 text-xl transition {{ request()->routeIs('dashboard.visitors') ? 'text-[#B71C1C] bg-[#B71C1C]/20' : '' }}">
            <i class="bi bi-people-fill"></i>
            <span
                class="absolute -top-10 left-1/2 -translate-x-1/2 bg-black text-white text-xs font-medium px-2 py-1 rounded-md opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap transition">
                Pengunjung
            </span>
        </a>
    @endif

    @if ($canProduk)
        {{-- Divider cuma dipasang kalau Produk adalah item pertama yang muncul
            (Orders & Pengunjung disembunyikan) — biar gak ada divider dobel/nyangkut --}}
        @if (!$canOrders && !$canVisitors)
            <div class="w-px h-6 bg-white/10 mx-1"></div>
        @endif

        {{-- Input Produk (1 halaman, gabungan Clothes/Accessories lewat dropdown kategori) --}}
        <a href="{{ route('dashboard.produk') }}"
            class="group relative flex items-center justify-center w-12 h-12 rounded-xl text-white/85 hover:text-white hover:bg-white/10 text-xl transition {{ request()->routeIs('dashboard.produk') ? 'text-[#B71C1C] bg-[#B71C1C]/20' : '' }}">
            <i class="bi bi-grid-fill"></i>
            <span
                class="absolute -top-10 left-1/2 -translate-x-1/2 bg-black text-white text-xs font-medium px-2 py-1 rounded-md opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap transition">
                Input Produk
            </span>
        </a>
    @endif

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

    <a href="{{ route('crew.logout') }}" onclick="event.preventDefault(); confirmCrewLogout();"
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
        class="relative flex flex-col items-center gap-1.5 {{ request()->routeIs('dashboard') ? 'text-[#B71C1C]' : 'text-white' }}">
        <span class="relative inline-block">
            <i class="bi bi-house-door-fill text-3xl"></i>
            @if ($navLowStockCount > 0)
                <span
                    class="absolute -top-1 -right-2 flex items-center justify-center min-w-4 h-4 px-1 rounded-full bg-amber-400 text-black text-[9px] font-bold">
                    {{ $navLowStockCount > 9 ? '9+' : $navLowStockCount }}
                </span>
            @endif
        </span>
        <span class="text-[10px] font-semibold uppercase tracking-wide">Dashboard</span>
    </a>

    @if ($canOrders)
        <a href="{{ route('dashboard.orders') }}"
            class="flex flex-col items-center gap-1.5 {{ request()->routeIs('dashboard.orders') ? 'text-[#B71C1C]' : 'text-white' }}">
            <i class="bi bi-box-seam text-3xl"></i>
            <span class="text-[10px] font-semibold uppercase tracking-wide">Orders</span>
        </a>
    @endif

    @if ($canVisitors)
        <a href="{{ route('dashboard.visitors') }}"
            class="flex flex-col items-center gap-1.5 {{ request()->routeIs('dashboard.visitors') ? 'text-[#B71C1C]' : 'text-white' }}">
            <i class="bi bi-people-fill text-3xl"></i>
            <span class="text-[10px] font-semibold uppercase tracking-wide">Pengunjung</span>
        </a>
    @endif

    @if ($canProduk)
        <a href="{{ route('dashboard.produk') }}"
            class="flex flex-col items-center gap-1.5 {{ request()->routeIs('dashboard.produk') ? 'text-[#B71C1C]' : 'text-white' }}">
            <i class="bi bi-grid-fill text-3xl"></i>
            <span class="text-[10px] font-semibold uppercase tracking-wide">Input Produk</span>
        </a>
    @endif

    <a href="{{ route('dashboard.import-export') }}"
        class="flex flex-col items-center gap-1.5 {{ request()->routeIs('dashboard.import-export') ? 'text-[#B71C1C]' : 'text-white' }}">
        <i class="bi bi-file-earmark-excel text-3xl"></i>
        <span class="text-[10px] font-semibold uppercase tracking-wide">Import & Export</span>
    </a>
    <a href="{{ route('crew.logout') }}" onclick="event.preventDefault(); confirmCrewLogout();"
        class="flex flex-col items-center gap-1.5 text-[#B71C1C] hover:text-[#891212]">
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

    // ---- Konfirmasi sebelum logout staff, biar gak ke-tap gak sengaja ----
    function confirmCrewLogout() {
        Swal.fire({
            icon: 'question',
            title: 'Keluar dari dashboard?',
            text: 'Kamu perlu login lagi untuk mengakses dashboard.',
            showCancelButton: true,
            confirmButtonText: 'Ya, keluar',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#B71C1C',
            cancelButtonColor: '#6b7280',
            reverseButtons: true,
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ route('crew.logout') }}";
            }
        });
    }
</script>
