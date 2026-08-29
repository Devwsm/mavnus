{{--
    Dropdown Kategori. Pas dipilih, field detail kategori yang sesuai
    (clothes / accessories) yang ditampilkan, sisanya disembunyikan DAN
    di-disable (biar gak ikut ke-submit/divalidasi server pas kategori lain
    yang dipilih).

    Props:
    - selected : kategori aktif saat ini ("clothes" / "accessories")
    - suffix   : biar id gak bentrok kalau ada banyak instance di 1 halaman
    - disabled : true kalau kategori gak boleh diubah lagi (mode edit) —
        tetep kirim value-nya lewat hidden input
--}}
@props([
    'selected' => 'clothes',
    'suffix' => '',
    'disabled' => false,
])

<div>
    <label for="inputCategory{{ $suffix }}" class="block text-sm font-semibold mb-1.5 text-white">Kategori</label>
    <select id="inputCategory{{ $suffix }}" name="category" @disabled($disabled)
        onchange="mavnusToggleCategoryFields('{{ $suffix }}')"
        {{ $attributes->merge(['class' => 'w-full bg-black border border-white/10 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-[#B71C1C] disabled:opacity-50']) }}>
        <option value="clothes" @selected($selected === 'clothes')>Clothes</option>
        <option value="accessories" @selected($selected === 'accessories')>Accessories</option>
    </select>

    @if ($disabled)
        {{-- Select di-disable gak ikut ke-submit, jadi kirim value asli lewat hidden input --}}
        <input type="hidden" name="category" value="{{ $selected }}">
        <p class="text-white/30 text-xs mt-1.5">Kategori gak bisa diubah setelah produk dibuat.</p>
    @endif
</div>

@once
    <script>
        // Toggle blok field kategori (clothes / accessories) sesuai pilihan dropdown.
        // Field yang disembunyikan di-disable biar gak ikut request submit &
        // gak kena validasi "required_if" di server buat kategori yang gak dipilih.
        function mavnusToggleCategoryFields(suffix) {
            const select = document.getElementById('inputCategory' + suffix);
            if (!select) return;
            const category = select.value;

            const boxIds = [
                'categoryFields-clothes' + suffix,
                'categoryFields-clothes-variants' + suffix,
                'categoryFields-accessories' + suffix,
            ];

            boxIds.forEach(id => {
                const box = document.getElementById(id);
                if (!box) return;

                const isClothesBox = id.startsWith('categoryFields-clothes');
                const isActive = isClothesBox ? category === 'clothes' : category === 'accessories';

                box.style.display = isActive ? 'flex' : 'none';
                box.querySelectorAll('input, select, textarea').forEach(el => {
                    el.disabled = !isActive;
                });
            });
        }
    </script>
@endonce
