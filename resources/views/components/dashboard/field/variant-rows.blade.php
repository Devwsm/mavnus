{{--
    Baris dinamis Ukuran & Stok, khusus kategori Clothes.
    JS-nya di-generic-in pakai "suffix" biar 1 set fungsi bisa dipakai
    bareng-bareng buat form create DAN semua modal edit di halaman yang sama
    (sebelumnya logic ini di-duplicate manual di 2 tempat).

    Props:
    - suffix   : id scoping (form create = '', modal edit = '-editModal-{id}')
    - variants : array of ['size' => ..., 'stock' => ...] (buat edit, opsional)
    - active   : true kalau kategori Clothes yang lagi kepilih
--}}
@props([
    'suffix' => '',
    'variants' => null,
    'active' => true,
])
@php
    $rows = $variants && count($variants) > 0 ? $variants : [['size' => 'S', 'stock' => '']];
@endphp

<div id="categoryFields-clothes-variants{{ $suffix }}"
    class="flex-col gap-4 bg-[#0D0D0D] border border-white/10 rounded-xl p-6"
    style="display: {{ $active ? 'flex' : 'none' }};">
    <h2 class="text-xs font-semibold uppercase tracking-widest text-[#B71C1C]">Ukuran & Stok</h2>

    <div id="variantRows{{ $suffix }}" class="flex flex-col gap-3">
        @foreach ($rows as $i => $row)
            <div class="variant-row{{ $suffix }} flex items-center gap-3">
                <select name="variants[{{ $i }}][size]" @disabled(!$active)
                    class="variant-size min-w-0 bg-black border border-white/10 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-[#B71C1C] disabled:opacity-50">
                    @foreach (['S', 'M', 'L', 'XL'] as $size)
                        <option value="{{ $size }}" @selected(($row['size'] ?? 'S') === $size)>{{ $size }}
                        </option>
                    @endforeach
                </select>
                <input type="number" name="variants[{{ $i }}][stock]" value="{{ $row['stock'] ?? '' }}"
                    placeholder="Stock" @disabled(!$active)
                    class="variant-stock min-w-0 flex-1 bg-black border border-white/10 rounded-lg px-4 py-2 text-white placeholder-white/30 focus:outline-none focus:border-[#B71C1C] disabled:opacity-50">
                <button type="button"
                    class="removeVariantBtn text-white/40 hover:text-[#B71C1C] text-xl px-2 shrink-0">
                    <i class="bi bi-x-circle"></i>
                </button>
            </div>
        @endforeach
    </div>

    <button type="button" onclick="mavnusAddVariantRow('{{ $suffix }}')"
        class="self-start text-sm font-semibold text-[#B71C1C] hover:text-[#891212] transition">
        + Tambah Ukuran
    </button>
</div>

@once
    <script>
        // ---- Ukuran & Stok (generic, dipakai form create + semua modal edit) ----
        function mavnusGetVariantRows(suffix) {
            return document.querySelectorAll('.variant-row' + suffix);
        }

        function mavnusUpdateSizeAvailability(suffix) {
            const rows = mavnusGetVariantRows(suffix);
            const selectedSizes = Array.from(rows).map(row => row.querySelector('.variant-size').value);

            rows.forEach(row => {
                const select = row.querySelector('.variant-size');
                Array.from(select.options).forEach(option => {
                    option.disabled = selectedSizes.includes(option.value) && option.value !== select.value;
                });
            });
        }

        function mavnusGetNextAvailableSize(suffix, excludeSelect) {
            const rows = mavnusGetVariantRows(suffix);
            const usedSizes = Array.from(rows)
                .map(row => row.querySelector('.variant-size'))
                .filter(select => select !== excludeSelect)
                .map(select => select.value);

            const allSizes = ['S', 'M', 'L', 'XL'];
            return allSizes.find(size => !usedSizes.includes(size)) || '';
        }

        function mavnusBindVariantRow(row, suffix) {
            row.querySelector('.variant-size').addEventListener('change', () => mavnusUpdateSizeAvailability(
                suffix));
            row.querySelector('.removeVariantBtn').addEventListener('click', () => {
                if (mavnusGetVariantRows(suffix).length > 1) {
                    row.remove();
                    mavnusUpdateSizeAvailability(suffix);
                }
            });
        }

        function mavnusAddVariantRow(suffix) {
            const container = document.getElementById('variantRows' + suffix);
            const nextSize = mavnusGetNextAvailableSize(suffix, null);

            if (!nextSize) {
                if (window.Swal) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Semua ukuran sudah dipilih',
                        text: 'Ukuran S, M, L, XL sudah semuanya dipakai. Tidak ada ukuran tersisa untuk ditambahkan.',
                        confirmButtonColor: '#1C1CB7',
                    });
                }
                return;
            }

            const rows = mavnusGetVariantRows(suffix);
            const index = rows.length;
            const newRow = rows[0].cloneNode(true);
            const select = newRow.querySelector('.variant-size');
            const stockInput = newRow.querySelector('.variant-stock');

            select.disabled = false;
            stockInput.disabled = false;
            select.name = `variants[${index}][size]`;
            select.value = nextSize;
            stockInput.name = `variants[${index}][stock]`;
            stockInput.value = '';

            container.appendChild(newRow);
            mavnusBindVariantRow(newRow, suffix);
            mavnusUpdateSizeAvailability(suffix);
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[id^="variantRows"]').forEach(container => {
                const suffix = container.id.replace('variantRows', '');
                mavnusGetVariantRows(suffix).forEach(row => mavnusBindVariantRow(row, suffix));
                mavnusUpdateSizeAvailability(suffix);
            });
        });
    </script>
@endonce
