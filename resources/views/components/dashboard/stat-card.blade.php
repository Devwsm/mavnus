{{-- Kartu angka ringkasan. $valueClass dipakai untuk mewarnai angka (mis. merah kalau ada yang perlu perhatian). --}}
@props(['label', 'value', 'valueClass' => '', 'note' => null])
<div class="bg-[#0D0D0D] border border-white/10 rounded-2xl p-4 flex flex-col gap-1">
    <span class="text-white/40 text-[11px] uppercase tracking-wide">{{ $label }}</span>
    <span class="text-xl lg:text-2xl font-bold {{ $valueClass }}">{{ $value }}</span>
    @if ($note)
        <span class="text-white/30 text-[10px]">{{ $note }}</span>
    @endif
</div>
