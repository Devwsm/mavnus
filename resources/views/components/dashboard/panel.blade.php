{{-- Kotak berjudul dengan link opsional di pojok kanan atas. Isi kotak = slot. --}}
@props(['title', 'href' => null, 'link' => null])
<section
    {{ $attributes->merge(['class' => 'bg-white/3 border border-white/8 rounded-2xl p-5 flex flex-col gap-3']) }}>
    <div class="flex items-center justify-between gap-3">
        <h2 class="text-white/80 text-sm font-semibold">{{ $title }}</h2>
        @if ($href)
            <a href="{{ $href }}"
                class="text-white/40 hover:text-white text-xs transition shrink-0">{{ $link }}</a>
        @endif
    </div>
    {{ $slot }}
</section>
