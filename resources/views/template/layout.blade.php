<!DOCTYPE html>
<html lang="id" class="bg-black">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#000000">

    <title>@yield('title', 'Mavnus - Clothing & Accessories')</title>
    <meta name="description" content="@yield('meta_description', 'Mavnus - Belanja clothes dan accessories original dengan kualitas terbaik.')">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Open Graph / Social share --}}
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:locale" content="id_ID">
    <meta property="og:site_name" content="Mavnus">
    <meta property="og:title" content="@yield('title', 'Mavnus - Clothing & Accessories')">
    <meta property="og:description" content="@yield('meta_description', 'Mavnus - Belanja clothes dan accessories original dengan kualitas terbaik.')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="@yield('og_image', asset('aset/logo/Whisnu-Santika_Logo-2025-2-White.png'))">
    <meta name="twitter:card" content="summary_large_image">

    {{-- Dipasang di <head> (bukan di app.js) supaya SUDAH ada sebelum script inline di halaman jalan.
        Semua teks dari server/API yang disisipkan lewat innerHTML WAJIB lewat fungsi ini,
        kalau tidak, nama produk berisi <script> / <img onerror> akan dieksekusi di browser pembeli. --}}
    <script>
        function escapeHtml(value) {
            return String(value ?? '').replace(/[&<>"']/g, (ch) => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;',
            })[ch]);
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon"
        href="{{ \App\Support\Img::url('aset/logo/favicon-192.png', 'aset/logo/Whisnu-Santika_Logo-2025-2-White.png') }}"
        type="image/png">
    @stack('head')
</head>

<body class="bg-black flex flex-col w-full">
    @include('components/navbar')
    <div class="flex flex-col justify-center items-center">
        @yield('content')
    </div>
    @include('components/footer')
</body>
