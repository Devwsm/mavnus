{{-- Keadaan kosong yang menjelaskan: apa yang akan muncul di sini dan kapan. --}}
@props(['icon' => 'bi-inbox', 'title', 'text' => null])
<div class="flex flex-col items-center text-center gap-1.5 py-8 px-4">
    <i class="bi {{ $icon }} text-white/20 text-3xl"></i>
    <p class="text-white/60 text-sm font-medium">{{ $title }}</p>
    @if ($text)
        <p class="text-white/35 text-xs max-w-xs">{{ $text }}</p>
    @endif
</div>
