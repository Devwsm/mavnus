{{-- desktop --}}
<div class="nav z-50 fixed bottom-5 left-1/2 -translate-x-1/2 hidden lg:flex gap-2">
    <div class="flex justify-center items-center p-6 rounded-lg bg-[#1A1A1B] shrink-0">
        <a href="{{ route('dashboard') }}" class="text-white text-[2rem]">
            <i class="bi bi-house-door-fill"></i>
        </a>
    </div>
    <div class="flex justify-center items-center gap-4 p-6 rounded-lg bg-[#1A1A1B] shrink-0">
        <i class="bi bi-bag-fill text-white text-[2rem]"></i>
    </div>
    <div class="flex justify-center items-center p-6 rounded-lg bg-[#1A1A1B] shrink-0">
        <a href="{{ route('logout') }}" class="text-[#B71C1C] hover:text-[#891212] text-[2rem]"><i
                class="bi bi-box-arrow-right"></i></a>
    </div>
</div>
{{-- ↑ DESKTOP DITUTUP DI SINI, sebelum blok mobile dimulai --}}


{{-- Mobile trigger --}}
<button id="dashOpenBtn"
    class="fixed z-50 bottom-5 right-5 lg:hidden flex justify-center items-center h-14 w-14 rounded-full bg-[#1A1A1B] text-[#F5F1E6] text-2xl shadow-lg">
    <i class="bi bi-list"></i>
</button>

{{-- Overlay penutup sisa dashboard --}}
<div id="dashMobileOverlay"
    class="fixed inset-0 bg-black/50 z-55
    opacity-0 pointer-events-none transition-opacity duration-300 lg:hidden">
</div>

{{-- Mobile Fullscreen --}}
<div id="dashMobileMenu"
    class="fixed top-0 right-0 h-full w-1/2 bg-black text-white z-60
    flex flex-col items-center justify-center gap-8 border-r-gray-200
    translate-x-full transition-transform duration-300 lg:hidden">

    <a href="{{ route('dashboard') }}" class="menu-link flex items-center gap-3">
        <i class="bi bi-house-door-fill text-3xl"></i>
    </a>
    <a href="{{ route('dashboard') }}" class="menu-link flex items-center gap-3">
        <i class="bi bi-bag-fill text-3xl"></i>
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
