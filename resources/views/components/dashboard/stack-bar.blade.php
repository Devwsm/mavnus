{{--
    Bar proporsi (satu batang terbagi beberapa warna) + legenda.
    $segments = [['label' => ..., 'value' => angka, 'color' => class warna, 'text' => teks nilai di legenda], ...]
    $total = pembagi untuk menghitung lebar tiap bagian.
--}}
@props(['segments', 'total'])
<div class="w-full h-3 rounded-full overflow-hidden flex bg-white/5">
    @foreach ($segments as $segment)
        <div class="h-full {{ $segment['color'] }}" style="width: {{ ($segment['value'] / $total) * 100 }}%"></div>
    @endforeach
</div>
<div class="flex flex-wrap gap-x-5 gap-y-2 text-xs">
    @foreach ($segments as $segment)
        <span class="flex items-center gap-1.5 text-white/60">
            <span class="w-2 h-2 rounded-full {{ $segment['color'] }}"></span>
            {{ $segment['label'] }} ({{ $segment['text'] ?? $segment['value'] }})
        </span>
    @endforeach
</div>
