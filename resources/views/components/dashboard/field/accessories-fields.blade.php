{{--
    Blok field khusus kategori Accessories: Tipe (keychain/sticker/totebag) & Stok.
    Accessories gak pakai variant kaya clothes — stok cuma 1 angka per produk.

    Props:
    - suffix : id scoping (form create = '', modal edit = '-editModal-{id}')
    - type   : nilai awal tipe accessory (buat edit)
    - stock  : nilai awal stok (buat edit)
    - active : true kalau kategori ini yang lagi kepilih
    - boxed  : true = bungkus dengan card ala form create,
        false = polos ala grid di dalam modal edit
--}}
@props([
    'suffix' => '',
    'type' => null,
    'stock' => null,
    'active' => false,
    'boxed' => true,
])

<div id="categoryFields-accessories{{ $suffix }}"
    class="flex-col gap-4 {{ $boxed ? 'bg-[#0D0D0D] border border-white/10 rounded-xl p-6' : '' }}"
    style="display: {{ $active ? 'flex' : 'none' }};">
    <h2 class="text-xs font-semibold uppercase tracking-widest text-[#B71C1C]">Detail Accessories</h2>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="inputAccessoryType{{ $suffix }}" class="block text-sm font-semibold mb-1.5 text-white">Tipe
                Aksesoris</label>
            <select id="inputAccessoryType{{ $suffix }}" name="accessory_type" @disabled(!$active)
                class="w-full bg-black border border-white/10 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-[#B71C1C] disabled:opacity-50">
                <option value="keychain" @selected($type === 'keychain')>Keychain</option>
                <option value="sticker" @selected($type === 'sticker')>Sticker</option>
                <option value="totebag" @selected($type === 'totebag')>Totebag</option>
            </select>
        </div>
        <x-dashboard.field.text-input label="Stok" name="stock" type="number" :value="$stock" placeholder="0"
            :suffix="$suffix" :disabled="!$active" />
    </div>
</div>
