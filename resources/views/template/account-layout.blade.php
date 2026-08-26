{{--
    Layout khusus halaman akun ("Akun Saya", "Riwayat Pesanan", "Edit Profil").
    Beda dari template.layout: TANPA footer besar, diganti bottom nav fixed
    gaya app biar user gampang pindah antar halaman akun (mirip Shopee/Tokopedia).

    Section yang bisa diisi halaman turunan:
    - $active (variabel Blade, wajib) → 'home' | 'orders' | 'account' untuk highlight tab bottom nav
    - @section('hideBottomNav') isi apa aja (mis. 'yes') → bottom nav disembunyikan
      (dipakai account-edit.blade.php karena udah ada sticky "Simpan Perubahan" sendiri)
--}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Mavnus - Clothing, Accessories & Albums')</title>
    <meta name="description" content="@yield('meta_description', 'Mavnus - Belanja clothes, accessories, dan albums original dengan kualitas terbaik.')">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Open Graph / Social share --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Mavnus">
    <meta property="og:title" content="@yield('title', 'Mavnus - Clothing, Accessories & Albums')">
    <meta property="og:description" content="@yield('meta_description', 'Mavnus - Belanja clothes, accessories, dan albums original dengan kualitas terbaik.')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="@yield('og_image', asset('aset/logo/Whisnu-Santika_Logo-2025-2-White.png'))">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" href="{{ asset('aset/logo/Whisnu-Santika_Logo-2025-2-White.png') }}" type="image/png">
</head>

<body class="bg-black flex flex-col w-full">
    @include('components/navbar')
    <div class="flex flex-col justify-center items-center w-full">
        @yield('content')
    </div>
    @hasSection('hideBottomNav')
    @else
        @include('components.account.bottom-nav', ['active' => $active ?? 'account'])
    @endif
</body>

</html>
