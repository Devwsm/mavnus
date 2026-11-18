{{--
    Navbar dashboard (desktop pill bawah + mobile menu samping). Semua staf yang login melihat
    menu yang sama; daftar menu cukup ditulis sekali di $menu lalu dipakai oleh dua tampilan.
--}}
@php
    // 'active' = pola nama route (dipakai request()->routeIs) supaya halaman detail ikut menyorot menunya
    $menu = [
        [
            'route' => 'dashboard',
            'icon' => 'bi-house-door-fill',
            'label' => 'Dashboard',
            'active' => 'dashboard',
            'badge' => $navLowStockCount ?? 0,
        ],
        ['route' => 'dashboard.orders', 'icon' => 'bi-box-seam', 'label' => 'Orders', 'active' => 'dashboard.orders*'],
        [
            'route' => 'dashboard.members',
            'icon' => 'bi-person-lines-fill',
            'label' => 'Anggota',
            'active' => 'dashboard.members*',
        ],
        [
            'route' => 'dashboard.visitors',
            'icon' => 'bi-people-fill',
            'label' => 'Pengunjung',
            'active' => 'dashboard.visitors*',
        ],
        [
            'route' => 'dashboard.produk',
            'icon' => 'bi-bag-plus-fill',
            'label' => 'Input Produk',
            'active' => 'dashboard.produk',
        ],
        [
            'route' => 'dashboard.import-export',
            'icon' => 'bi-file-earmark-excel',
            'label' => 'Import & Export',
            'active' => 'dashboard.import-export',
        ],
    ];
    $tooltipClass =
        'absolute -top-10 left-1/2 -translate-x-1/2 bg-black text-white text-xs font-medium px-2 py-1 rounded-md opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap transition';
@endphp

{{-- desktop --}}
<div
    class="nav z-50 fixed bottom-5 left-1/2 -translate-x-1/2 hidden lg:flex items-center gap-1 bg-[#0D0D0D] rounded-2xl px-2 py-2">

    @foreach ($menu as $item)
        @unless ($loop->first)
            <div class="w-px h-6 bg-white/10 mx-1"></div>
        @endunless

        <a href="{{ route($item['route']) }}"
            class="group relative flex items-center justify-center w-12 h-12 rounded-xl text-white/85 hover:text-white hover:bg-white/10 text-xl transition {{ request()->routeIs($item['active']) ? 'text-[#B71C1C] bg-[#B71C1C]/20' : '' }}">
            <i class="bi {{ $item['icon'] }}"></i>
            @if (($item['badge'] ?? 0) > 0)
                <span
                    class="absolute top-1.5 right-1.5 flex items-center justify-center min-w-4 h-4 px-1 rounded-full bg-amber-400 text-black text-[9px] font-bold">
                    {{ $item['badge'] > 9 ? '9+' : $item['badge'] }}
                </span>
            @endif
            <span class="{{ $tooltipClass }}">{{ $item['label'] }}</span>
        </a>
    @endforeach

    <div class="w-px h-6 bg-white/10 mx-1"></div>

    <a href="#" role="button" onclick="event.preventDefault(); confirmCrewLogout();"
        class="group relative flex items-center justify-center w-12 h-12 rounded-xl text-[#B71C1C] hover:text-[#891212] hover:bg-[#B71C1C]/5 text-xl transition">
        <i class="bi bi-box-arrow-right"></i>
        <span class="{{ $tooltipClass }}">Logout</span>
    </a>
</div>

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

{{-- Mobile menu --}}
<div id="dashMobileMenu"
    class="fixed top-0 right-0 h-full w-1/2 bg-[#0D0D0D] text-white z-80
    flex flex-col items-center justify-center gap-6 border-r-gray-200
    translate-x-full transition-transform duration-300 lg:hidden overflow-y-auto py-10">

    @foreach ($menu as $item)
        <a href="{{ route($item['route']) }}"
            class="relative flex flex-col items-center gap-1.5 {{ request()->routeIs($item['active']) ? 'text-[#B71C1C]' : 'text-white' }}">
            <span class="relative inline-block">
                <i class="bi {{ $item['icon'] }} text-3xl"></i>
                @if (($item['badge'] ?? 0) > 0)
                    <span
                        class="absolute -top-1 -right-2 flex items-center justify-center min-w-4 h-4 px-1 rounded-full bg-amber-400 text-black text-[9px] font-bold">
                        {{ $item['badge'] > 9 ? '9+' : $item['badge'] }}
                    </span>
                @endif
            </span>
            <span class="text-[10px] font-semibold uppercase tracking-wide">{{ $item['label'] }}</span>
        </a>
    @endforeach

    <a href="#" role="button" onclick="event.preventDefault(); confirmCrewLogout();"
        class="flex flex-col items-center gap-1.5 text-[#B71C1C] hover:text-[#891212]">
        <i class="bi bi-box-arrow-right text-3xl"></i>
        <span class="text-[10px] font-semibold uppercase tracking-wide">Logout</span>
    </a>

    <button id="dashCloseBtn"
        class="fixed bottom-5 right-5 flex justify-center items-center h-14 w-14 rounded-full bg-white/10 border border-white/20 text-white text-3xl">
        <i class="bi bi-x"></i>
    </button>
</div>

{{-- Logout harus POST (bukan link GET) biar gak bisa dipicu lewat <img>/link dari situs lain --}}
<form id="crewLogoutForm" method="POST" action="{{ route('crew.logout') }}" class="hidden">@csrf</form>

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
                document.getElementById('crewLogoutForm').submit();
            }
        });
    }
</script>
