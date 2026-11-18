{{--
    Grafik batang mini. $items = [['label' => ..., 'value' => angka, 'text' => teks di atas batang], ...]
    $max = nilai tertinggi (skala), $color = class warna batang yang ada isinya.
--}}
@props(['items', 'max', 'color' => 'bg-[#B71C1C]'])
<div class="flex items-end justify-between gap-2 h-28">
    @foreach ($items as $item)
        <div class="flex-1 flex flex-col items-center gap-1.5 h-full justify-end">
            <span class="text-white/50 text-[10px] font-semibold text-center leading-tight">{{ $item['text'] }}</span>
            <div class="w-full rounded-t-md {{ $item['value'] > 0 ? $color : 'bg-white/10' }}"
                style="height: {{ max(4, ($item['value'] / $max) * 100) }}%"></div>
            <span class="text-white/30 text-[10px]">{{ $item['label'] }}</span>
        </div>
    @endforeach
</div>
