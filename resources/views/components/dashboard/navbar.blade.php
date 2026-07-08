{{-- desktop --}}
<div class="nav z-50 fixed bottom-5 left-1/2 -translate-x-1/2 hidden lg:flex gap-2">
    <div class="flex justify-center items-center p-6 rounded-lg bg-[#0D0D0D] shrink-0">
        <a href="{{ route('dashboard') }}"
            class="group relative text-white/70 hover:text-white text-[2rem] transition {{ request()->routeIs('dashboard') ? 'text-[#B71C1C]' : '' }}">
            <i class="bi bi-house-door-fill"></i>
            <span
                class="absolute -top-9 left-1/2 -translate-x-1/2 bg-black text-white text-xs font-medium px-2 py-1 rounded-md opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap transition">
                Dashboard
            </span>
        </a>
    </div>
    <div class="flex justify-center items-center gap-4 p-6 rounded-lg bg-[#0D0D0D] shrink-0">
        <a href="{{ route('dashboard.clothes') }}"
            class="group relative text-white/70 hover:text-white text-[2rem] transition {{ request()->routeIs('dashboard.clothes') ? 'text-[#B71C1C]' : '' }}">
            <i class="bi bi-bag-fill"></i>
            <span
                class="absolute -top-9 left-1/2 -translate-x-1/2 bg-black text-white text-xs font-medium px-2 py-1 rounded-md opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap transition">
                Clothes
            </span>
        </a>
        <a href="{{ route('dashboard') }}"
            class="group relative text-white/70 hover:text-white text-[2rem] transition {{ request()->routeIs('dashboard.accessories') ? 'text-[#B71C1C]' : '' }}">
            <i class="bi bi-gem"></i>
            <span
                class="absolute -top-9 left-1/2 -translate-x-1/2 bg-black text-white text-xs font-medium px-2 py-1 rounded-md opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap transition">
                Accessories
            </span>
        </a>
        <a href="{{ route('dashboard') }}"
            class="group relative text-white/70 hover:text-white text-[2rem] transition {{ request()->routeIs('dashboard.albums') ? 'text-[#B71C1C]' : '' }}">
            <i class="bi bi-disc-fill"></i>
            <span
                class="absolute -top-9 left-1/2 -translate-x-1/2 bg-black text-white text-xs font-medium px-2 py-1 rounded-md opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap transition">
                Albums
            </span>
        </a>
    </div>
    <div class="flex justify-center items-center p-6 rounded-lg bg-[#0D0D0D] shrink-0">
        <a href="{{ route('logout') }}"
            class="group relative text-[#B71C1C] hover:text-[#891212] text-[2rem] transition">
            <i class="bi bi-box-arrow-right"></i>
            <span
                class="absolute -top-9 left-1/2 -translate-x-1/2 bg-black text-white text-xs font-medium px-2 py-1 rounded-md opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap transition">
                Logout
            </span>
        </a>
    </div>
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
    flex flex-col items-center justify-center gap-8 border-r-gray-200
    translate-x-full transition-transform duration-300 lg:hidden">

    <a href="{{ route('dashboard') }}"
        class="flex flex-col items-center gap-1.5 {{ request()->routeIs('dashboard') ? 'text-[#B71C1C]' : 'text-white' }}">
        <i class="bi bi-house-door-fill text-3xl"></i>
        <span class="text-[10px] font-semibold uppercase tracking-wide">Dashboard</span>
    </a>
    <a href="{{ route('dashboard.clothes') }}"
        class="flex flex-col items-center gap-1.5 {{ request()->routeIs('dashboard.clothes') ? 'text-[#B71C1C]' : 'text-white' }}">
        <i class="bi bi-bag-fill text-3xl"></i>
        <span class="text-[10px] font-semibold uppercase tracking-wide">Clothes</span>
    </a>
    <a href="{{ route('dashboard') }}"
        class="flex flex-col items-center gap-1.5 {{ request()->routeIs('dashboard.accessories') ? 'text-[#B71C1C]' : 'text-white' }}">
        <i class="bi bi-gem text-3xl"></i>
        <span class="text-[10px] font-semibold uppercase tracking-wide">Accessories</span>
    </a>
    <a href="{{ route('dashboard') }}"
        class="flex flex-col items-center gap-1.5 {{ request()->routeIs('dashboard.albums') ? 'text-[#B71C1C]' : 'text-white' }}">
        <i class="bi bi-disc-fill text-3xl"></i>
        <span class="text-[10px] font-semibold uppercase tracking-wide">Albums</span>
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
</script>
