{{-- Kartu angka ringkasan. Kalau $href diisi, seluruh kartu bisa diklik. $note = keterangan kecil di bawah angka. --}}
@props(['label', 'value', 'valueClass' => '', 'note' => null, 'href' => null])
<{{ $href ? 'a' : 'div' }} @if ($href) href="{{ $href }}" @endif
    class="bg-white/3 border border-white/8 rounded-2xl p-4 flex flex-col gap-1 {{ $href ? 'hover:bg-white/6 transition' : '' }}">
    <span class="text-white/50 text-xs">{{ $label }}</span>
    <span class="text-xl lg:text-2xl font-bold {{ $valueClass }}">{{ $value }}</span>
    @if ($note)
        <span class="text-white/35 text-[11px] leading-snug">{{ $note }}</span>
    @endif
    </{{ $href ? 'a' : 'div' }}>
