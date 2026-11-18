{{-- Lingkaran inisial nama (dipakai daftar anggota). --}}
@props(['name', 'size' => 'w-9 h-9 text-sm'])
<div {{ $attributes->merge(['class' => "{$size} rounded-full bg-[#B71C1C]/20 text-[#e05656] font-bold flex items-center justify-center shrink-0 uppercase"]) }}
    aria-hidden="true">
    {{ \Illuminate\Support\Str::substr($name, 0, 1) }}
</div>
