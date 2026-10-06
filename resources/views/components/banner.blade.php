@php
    $banner1600 = \App\Support\Img::url('aset/banner/banner-1600.webp', 'aset/banner/banner.jpg');
    $banner800 = \App\Support\Img::url('aset/banner/banner-800.webp', 'aset/banner/banner.jpg');
@endphp
<section class="flex flex-col w-full bg-black overflow-hidden pt-22 md:pt-18 lg:pt-28">

    <!-- ===================== Ad / Banner Placeholder ===================== -->
    <div class="flex flex-col w-full justify-center items-center z-30 transition-transform duration-300">
        {{-- Banner = elemen LCP. webp 68KB (dulu jpg 101KB); HP mendapat versi 800px (22KB). --}}
        <img src="{{ $banner1600 }}" srcset="{{ $banner800 }} 800w, {{ $banner1600 }} 1600w" sizes="100vw"
            alt="Mavnus - Official Merchandise Whisnu Santika" width="1600" height="400" loading="eager"
            fetchpriority="high" decoding="async" class="object-cover w-full rounded-lg">
    </div>
</section>
