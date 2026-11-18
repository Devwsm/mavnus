{{-- Foto kecil produk (kotak 36px). $path = path di disk public, kosong = kotak polos. --}}
@props(['path' => null, 'alt' => ''])
<div class="w-9 h-9 rounded-lg bg-white/5 overflow-hidden shrink-0">
    @if ($path)
        <img src="{{ Storage::url($path) }}" alt="{{ $alt }}" class="w-full h-full object-cover" loading="lazy">
    @endif
</div>
