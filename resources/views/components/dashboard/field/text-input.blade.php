{{--
    Field generik untuk input text/number bertema dashboard.
    Dipakai di form Tambah Produk & modal Edit — biar style-nya konsisten
    tanpa copy-paste class Tailwind di banyak tempat.

    Props:
    - label       : teks label di atas input
    - name        : atribut name (boleh pakai bracket, misal "variants[0][stock]")
    - type        : text|number|dll, default "text"
    - value       : nilai awal (buat form edit)
    - placeholder : placeholder input
    - suffix      : dipasang di belakang id biar gak bentrok kalau komponen ini
        dipakai berkali-kali di 1 halaman (form create + banyak modal edit)
--}}
@props(['label', 'name', 'type' => 'text', 'value' => null, 'placeholder' => null, 'suffix' => ''])
@php $inputId = 'input-' . $name . $suffix; @endphp

<div>
    <label for="{{ $inputId }}" class="block text-sm font-semibold mb-1.5 text-white">{{ $label }}</label>
    <input type="{{ $type }}" id="{{ $inputId }}" name="{{ $name }}" value="{{ $value }}"
        @if ($placeholder) placeholder="{{ $placeholder }}" @endif
        {{ $attributes->merge(['class' => 'w-full bg-black border border-white/10 rounded-lg px-4 py-2.5 text-white placeholder-white/30 focus:outline-none focus:border-[#B71C1C]']) }}>
</div>
