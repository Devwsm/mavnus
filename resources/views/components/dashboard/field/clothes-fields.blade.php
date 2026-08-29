{{--
    Blok field khusus kategori Clothes: Warna & Material.

    Props:
    - suffix   : id scoping (form create = '', modal edit = '-editModal-{id}')
    - color    : nilai awal warna (buat edit)
    - material : nilai awal material (buat edit)
    - active   : true kalau kategori ini yang lagi kepilih (kontrol display awal)
    - boxed    : true = bungkus dengan card ala form create,
                false = polos ala grid di dalam modal edit
--}}
@props([
    'suffix' => '',
    'color' => null,
    'material' => null,
    'active' => true,
    'boxed' => true,
])

<div id="categoryFields-clothes{{ $suffix }}"
    class="flex-col gap-4 {{ $boxed ? 'bg-[#0D0D0D] border border-white/10 rounded-xl p-6' : '' }}"
    style="display: {{ $active ? 'flex' : 'none' }};">
    <h2 class="text-xs font-semibold uppercase tracking-widest text-[#B71C1C]">Detail Clothes</h2>

    <div class="grid grid-cols-2 gap-4">
        <x-dashboard.field.text-input label="Warna" name="color" :value="$color" placeholder="Black / Blue...."
            :suffix="$suffix" :disabled="!$active" />
        <x-dashboard.field.text-input label="Material" name="material" :value="$material" placeholder="Cotton Combed 24s"
            :suffix="$suffix" :disabled="!$active" />
    </div>
</div>
