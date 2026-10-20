@php
    // Banner berisi teks yang ikut di-desain ke dalam gambar, jadi tidak bisa diterjemahkan lewat file lang/.
    // Solusinya: satu gambar per bahasa. Letakkan di public/aset/banner/ dengan nama:
    //   banner-id-1600.webp, banner-id-800.webp  (atau cukup banner-id.jpg)
    //   banner-en-1600.webp, banner-en-800.webp  (atau cukup banner-en.jpg)
    // Kalau file bahasa yang dipilih belum ada, otomatis jatuh ke banner-1600.webp / banner.jpg yang lama.
    $bannerLocale = app()->getLocale();
    $bannerPick = function (int $size) use ($bannerLocale): string {
        $candidates = [
            "aset/banner/banner-{$bannerLocale}-{$size}.webp",
            "aset/banner/banner-{$bannerLocale}.jpg",
            "aset/banner/banner-{$size}.webp",
            'aset/banner/banner.jpg',
        ];
        foreach ($candidates as $path) {
            $full = public_path($path);
            if (is_file($full) && filesize($full) > 0) {
                return asset($path);
            }
        }

        return asset('aset/banner/banner.jpg');
    };
    $banner1600 = $bannerPick(1600);
    $banner800 = $bannerPick(800);
@endphp
<section class="flex flex-col w-full bg-black overflow-hidden pt-22 md:pt-18 lg:pt-28">

    <!-- ===================== Ad / Banner Placeholder ===================== -->
    <div class="flex flex-col w-full justify-center items-center z-30 transition-transform duration-300">
        {{-- Banner = elemen LCP. webp 68KB (dulu jpg 101KB); HP mendapat versi 800px (22KB). --}}
        <img src="{{ $banner1600 }}" srcset="{{ $banner800 }} 800w, {{ $banner1600 }} 1600w" sizes="100vw"
            alt="{{ __('site.home.banner_alt') }}" width="1600" height="400" loading="eager" fetchpriority="high"
            decoding="async" class="object-cover w-full rounded-lg">
    </div>
</section>
