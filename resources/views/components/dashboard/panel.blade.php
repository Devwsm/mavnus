{{-- Kotak berjudul dengan link opsional di pojok kanan atas. Isi kotak = slot. --}}
@props(['title', 'href' => null, 'link' => null])
<section
    {{ $attributes->merge(['class' => 'bg-[#0D0D0D] border border-white/10 rounded-2xl p-5 flex flex-col gap-4']) }}>
    <div class="flex items-center justify-between">
        <h2 class="text-white/40 text-[11px] font-semibold uppercase tracking-widest">{{ $title }}</h2>
        @if ($href)
            <a href="{{ $href }}"
                class="text-white/40 hover:text-white text-xs transition">{{ $link }}</a>
        @endif
    </div>
    {{ $slot }}
</section>
