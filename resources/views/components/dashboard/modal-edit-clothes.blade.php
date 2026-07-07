@php $editId = 'editModal-' . $product->id_product; @endphp

{{-- Trigger --}}
<button type="button" onclick="toggleEditModal('{{ $editId }}', true)"
    class="flex-1 flex items-center justify-center gap-1.5 bg-white/5 hover:bg-white/10 text-white text-xs font-semibold py-2 rounded-lg transition">
    <i class="bi bi-pencil-square"></i>
    Edit
</button>

{{-- Modal --}}
<div id="{{ $editId }}" class="hidden fixed inset-0 z-100 items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" onclick="toggleEditModal('{{ $editId }}', false)">
    </div>

    <div class="relative bg-[#0D0D0D] border border-white/10 rounded-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto">

        <div class="flex items-center justify-between p-6 border-b border-white/10 sticky top-0 bg-[#0D0D0D] z-10">
            <h2 class="text-lg font-bold text-white uppercase tracking-wide">Edit {{ $product->name }}</h2>
            <button type="button" onclick="toggleEditModal('{{ $editId }}', false)"
                class="text-white/40 hover:text-white text-xl">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form action="{{ route('clothes.update', $product) }}" method="POST" enctype="multipart/form-data"
            class="flex flex-col gap-6 p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Data Dasar --}}
                <div class="flex flex-col gap-4">
                    <h3 class="text-xs font-semibold uppercase tracking-widest text-[#B71C1C]">Data Dasar</h3>
                    <div>
                        <label class="block text-sm font-semibold mb-1.5 text-white">Nama Produk</label>
                        <input type="text" name="name" value="{{ $product->name }}"
                            class="w-full bg-black border border-white/10 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-[#B71C1C]">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1.5 text-white">Harga</label>
                        <input type="number" name="price" value="{{ $product->price }}"
                            class="w-full bg-black border border-white/10 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-[#B71C1C]">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1.5 text-white">Deskripsi</label>
                        <input type="text" name="description" value="{{ $product->description }}"
                            class="w-full bg-black border border-white/10 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-[#B71C1C]">
                    </div>
                </div>

                {{-- Detail Clothes --}}
                <div class="flex flex-col gap-4">
                    <h3 class="text-xs font-semibold uppercase tracking-widest text-[#B71C1C]">Detail Clothes</h3>
                    <div>
                        <label class="block text-sm font-semibold mb-1.5 text-white">Warna</label>
                        <input type="text" name="color" value="{{ $product->clothes->color }}"
                            class="w-full bg-black border border-white/10 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-[#B71C1C]">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1.5 text-white">Material</label>
                        <input type="text" name="material" value="{{ $product->clothes->material }}"
                            class="w-full bg-black border border-white/10 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-[#B71C1C]">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Ukuran & Stok --}}
                <div class="flex flex-col gap-4">
                    <h3 class="text-xs font-semibold uppercase tracking-widest text-[#B71C1C]">Ukuran & Stok</h3>

                    <div id="variantRows-{{ $editId }}" class="flex flex-col gap-3">
                        @foreach ($product->clothes->variants as $i => $variant)
                            <div class="variant-row-{{ $editId }} flex items-center gap-3">
                                <select name="variants[{{ $i }}][size]"
                                    onchange="updateEditSizeAvailability('{{ $editId }}')"
                                    class="min-w-0 bg-black border border-white/10 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-[#B71C1C]">
                                    @foreach (['S', 'M', 'L', 'XL'] as $size)
                                        <option value="{{ $size }}" @selected($variant->size === $size)>
                                            {{ $size }}</option>
                                    @endforeach
                                </select>
                                <input type="number" name="variants[{{ $i }}][stock]"
                                    value="{{ $variant->stock }}" placeholder="Stok"
                                    class="min-w-0 flex-1 bg-black border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-[#B71C1C]">
                                <button type="button" onclick="removeEditVariantRow(this, '{{ $editId }}')"
                                    class="text-white/40 hover:text-[#B71C1C] text-xl px-2 shrink-0">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>

                    <button type="button" onclick="addEditVariantRow('{{ $editId }}')"
                        class="self-start text-sm font-semibold text-[#B71C1C] hover:text-[#891212] transition">
                        + Tambah Ukuran
                    </button>
                </div>

                {{-- Foto --}}
                <div class="flex flex-col gap-4">
                    <h3 class="text-xs font-semibold uppercase tracking-widest text-[#B71C1C]">Foto Saat Ini</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($product->images as $image)
                            <div class="w-14 h-14 rounded-md overflow-hidden border border-white/10">
                                <img src="{{ Storage::url($image->image_path) }}" alt="{{ $product->name }}"
                                    class="w-full h-full object-cover object-center">
                            </div>
                        @endforeach
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-1.5 text-white">Tambah Foto Baru (opsional)</label>
                        <input type="file" name="images[]" multiple accept="image/*"
                            class="w-full bg-black border border-white/10 rounded-lg px-4 py-2.5 text-white file:mr-4 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:bg-[#B71C1C] file:text-white file:text-sm file:font-semibold">
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2 pt-2 border-t border-white/6">
                <button type="button" onclick="toggleEditModal('{{ $editId }}', false)"
                    class="flex-1 bg-white/5 hover:bg-white/10 text-white text-sm font-semibold py-2.5 rounded-lg transition">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 bg-[#B71C1C] hover:bg-[#891212] text-white text-sm font-semibold py-2.5 rounded-lg transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

@once
    <script>
        function toggleEditModal(editId, show) {
            const modal = document.getElementById(editId);
            modal.classList.toggle('hidden', !show);
            modal.classList.toggle('flex', show);
        }

        function removeEditVariantRow(button, editId) {
            const rows = document.querySelectorAll('.variant-row-' + editId);
            if (rows.length <= 1) {
                alert('Minimal harus ada satu ukuran & stok.');
                return;
            }
            button.closest('.variant-row-' + editId).remove();
            updateEditSizeAvailability(editId);
        }

        function addEditVariantRow(editId) {
            const container = document.getElementById('variantRows-' + editId);
            const rows = container.querySelectorAll('.variant-row-' + editId);
            const allSizes = ['S', 'M', 'L', 'XL'];
            const usedSizes = Array.from(rows).map(row => row.querySelector('select').value);
            const nextSize = allSizes.find(size => !usedSizes.includes(size));

            if (!nextSize) {
                alert('Semua ukuran (S, M, L, XL) sudah dipilih.');
                return;
            }

            const index = rows.length;
            let newRow;

            if (rows.length > 0) {
                newRow = rows[0].cloneNode(true);
            } else {
                // Container kosong total (semua baris sempat dihapus) — bikin dari template.
                newRow = document.createElement('div');
                newRow.className = 'variant-row-' + editId + ' flex items-center gap-3';
                newRow.innerHTML = `
                    <select class="min-w-0 bg-black border border-white/10 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-[#B71C1C]">
                        <option value="S">S</option>
                        <option value="M">M</option>
                        <option value="L">L</option>
                        <option value="XL">XL</option>
                    </select>
                    <input type="number" placeholder="Stok"
                        class="min-w-0 flex-1 bg-black border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-[#B71C1C]">
                    <button type="button" class="text-white/40 hover:text-[#B71C1C] text-xl px-2 shrink-0">
                        <i class="bi bi-x-circle"></i>
                    </button>
                `;
            }

            const select = newRow.querySelector('select');
            const stockInput = newRow.querySelector('input[type="number"]');
            const removeBtn = newRow.querySelector('button');

            select.name = `variants[${index}][size]`;
            select.value = nextSize;
            select.setAttribute('onchange', `updateEditSizeAvailability('${editId}')`);

            stockInput.name = `variants[${index}][stock]`;
            stockInput.value = '';

            removeBtn.setAttribute('onclick', `removeEditVariantRow(this, '${editId}')`);

            container.appendChild(newRow);
            updateEditSizeAvailability(editId);
        }

        function updateEditSizeAvailability(editId) {
            const rows = document.querySelectorAll('.variant-row-' + editId);
            const selectedSizes = Array.from(rows).map(row => row.querySelector('select').value);

            rows.forEach(row => {
                const select = row.querySelector('select');
                Array.from(select.options).forEach(option => {
                    option.disabled = selectedSizes.includes(option.value) && option.value !== select.value;
                });
            });
        }
    </script>
@endonce
