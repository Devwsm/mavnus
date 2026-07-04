<div class="navbar-cover">
    <div id="navbar" class="fixed top-0 left-0 w-full bg-[#5E0006] text-white z-50 transition-transform duration-300">

        <!-- ===================== SECTION 1: Search | Logo | Account & Cart ===================== -->
        <div class="flex items-center justify-between px-4 py-4 border-b border-white/10 lg:border-none">
            <!-- Left -->
            <div class="w-1/3 flex items-center">
                <!-- Burger (mobile/tablet only) -->
                <button id="menuBtn" class="text-3xl lg:hidden">
                    <i class="bi bi-list"></i>
                </button>
                <!-- Search (desktop only) -->
                <button id="searchBtnDesktop" class="hidden lg:flex items-center gap-2 text-lg search-toggle-btn">
                    <i class="bi bi-search"></i>
                    <span class="uppercase text-sm font-semibold tracking-wide">Search</span>
                </button>
            </div>

            <!-- Logo (center, always) -->
            <div class="w-1/3 flex justify-center">
                <a href="{{ route('home') }}">
                    <img src="{{ asset('aset/logo/Whisnu-Santika_Logo-2025-White.png') }}" loading="lazy"
                        decoding="async" alt="whisnu-santika" class="object-cover hidden md:block w-52 rounded-lg">
                    <img src="{{ asset('aset/logo/Whisnu-Santika_Logo-2025-2-White.png') }}" loading="lazy"
                        decoding="async" alt="whisnu-santika" class="object-cover md:hidden w-52 rounded-lg">
                </a>
            </div>

            <!-- Right -->
            <div class="w-1/3 flex items-center justify-end gap-2 md:gap-4">
                <!-- Account & Cart (selalu tampil) -->
                <a href="{{ route('home') }}" class="inline-flex text-lg">
                    <i class="bi bi-person"></i>
                </a>
                <a href="{{ route('home') }}#cart" class="inline-flex text-lg">
                    <i class="bi bi-bag"></i>
                </a>
                <!-- Search (mobile/tablet saja, desktop pakai tombol di kiri) -->
                <button id="searchBtnMobile" class="text-lg lg:hidden search-toggle-btn">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </div>

        <!-- ===================== SECTION 2: T-Shirts | Accessories | Albums | Tour (desktop only) ===================== -->
        <div class="hidden lg:flex items-center justify-center gap-10 py-3 border-t border-white/10">
            <a href="{{ route('home') }}#store">
                <h1 class="font-bold uppercase text-sm tracking-wide">T-Shirts</h1>
            </a>
            <a href="{{ route('home') }}#store">
                <h1 class="font-bold uppercase text-sm tracking-wide">Accessories</h1>
            </a>
            <a href="{{ route('home') }}#new-music">
                <h1 class="font-bold uppercase text-sm tracking-wide">Albums</h1>
            </a>
            <a href="{{ route('home') }}#tour">
                <h1 class="font-bold uppercase text-sm tracking-wide">Tour</h1>
            </a>
        </div>
    </div>

    <!-- ===================== Search Bar (slides down under navbar, dipakai di semua ukuran layar) ===================== -->
    <div id="searchBar"
        class="fixed left-0 w-full bg-[#5E0006] text-white z-40 px-4 py-3
        -translate-y-full opacity-0 transition-all duration-300">
        <div class="flex items-center gap-3 bg-white/10 rounded-full px-4 py-2 lg:max-w-md lg:mx-auto">
            <i class="bi bi-search text-lg"></i>
            <input type="text" placeholder="Search"
                class="bg-transparent outline-none placeholder-white/70 text-white w-full text-sm">
        </div>
    </div>

    <!-- ===================== Mobile Menu Backdrop ===================== -->
    <div id="menuBackdrop"
        class="fixed inset-0 bg-black/50 z-55 opacity-0 pointer-events-none transition-opacity duration-300">
    </div>

    <!-- ===================== Mobile Half-Screen Drawer (Burger) ===================== -->
    <div id="mobileMenu"
        class="fixed top-0 left-0 h-full w-1/2 sm:w-2/5 bg-[#5E0006] text-white z-60
        flex flex-col items-start justify-center gap-7 px-8
        -translate-x-full transition-transform duration-300">
        <button id="closeBtn" class="absolute top-5 right-5 text-3xl">
            <i class="bi bi-x"></i>
        </button>

        <a href="{{ route('home') }}#store" class="menu-link">
            <h1 class="text-xl font-bold uppercase">T-Shirts</h1>
        </a>
        <a href="{{ route('home') }}#store" class="menu-link">
            <h1 class="text-xl font-bold uppercase">Accessories</h1>
        </a>
        <a href="{{ route('home') }}#new-music" class="menu-link">
            <h1 class="text-xl font-bold uppercase">Albums</h1>
        </a>
        <a href="{{ route('home') }}#tour" class="menu-link">
            <h1 class="text-xl font-bold uppercase">Tour</h1>
        </a>
    </div>
</div>

<script>
    const menuBtn = document.getElementById("menuBtn");
    const closeBtn = document.getElementById("closeBtn");
    const mobileMenu = document.getElementById("mobileMenu");
    const menuBackdrop = document.getElementById("menuBackdrop");
    const menuLinks = document.querySelectorAll(".menu-link");

    const searchBar = document.getElementById("searchBar");

    let scrollPosition = 0;
    let searchOpen = false;

    // ---------- Mobile Burger Menu ----------
    function openMenu() {
        menuOpen = true;

        scrollPosition = window.pageYOffset;

        mobileMenu.classList.remove("-translate-x-full");
        menuBackdrop.classList.remove("opacity-0", "pointer-events-none");
        menuBackdrop.classList.add("opacity-100", "pointer-events-auto");

        document.body.style.position = "fixed";
        document.body.style.top = `-${scrollPosition}px`;
        document.body.style.width = "100%";
    }

    function closeMenu() {
        mobileMenu.classList.add("-translate-x-full");
        menuBackdrop.classList.add("opacity-0", "pointer-events-none");
        menuBackdrop.classList.remove("opacity-100", "pointer-events-auto");

        document.body.style.position = "";
        document.body.style.top = "";
        document.body.style.width = "";

        window.scrollTo({
            top: scrollPosition,
            behavior: "instant"
        });

        lastScrollY = scrollPosition;

        menuOpen = false;
    }

    menuBtn.addEventListener("click", openMenu);
    closeBtn.addEventListener("click", closeMenu);
    menuBackdrop.addEventListener("click", closeMenu);

    menuLinks.forEach(link => {
        link.addEventListener("click", closeMenu);
    });

    // ---------- Search Bar (slide down under navbar, dipakai di semua ukuran layar) ----------
    function positionSearchBar() {
        const navbarHeight = document.getElementById("navbar").offsetHeight;
        searchBar.style.top = `${navbarHeight}px`;
    }

    function toggleSearchBar() {
        positionSearchBar();
        searchOpen = !searchOpen;

        if (searchOpen) {
            searchBar.classList.remove("-translate-y-full", "opacity-0");
        } else {
            searchBar.classList.add("-translate-y-full", "opacity-0");
        }
    }

    document.querySelectorAll(".search-toggle-btn").forEach(btn => {
        btn.addEventListener("click", toggleSearchBar);
    });
    window.addEventListener("resize", positionSearchBar);
</script>
