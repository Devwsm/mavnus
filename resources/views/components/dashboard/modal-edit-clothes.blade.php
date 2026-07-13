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

        <form id="editForm-{{ $editId }}" action="{{ route('clothes.update', $product) }}" method="POST"
            enctype="multipart/form-data" class="flex flex-col gap-6 p-6">
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
                    <div id="existingImages-{{ $editId }}" class="flex flex-wrap gap-2">
                        @foreach ($product->images as $image)
                            <div class="existing-image-{{ $editId }} relative w-14 h-14 rounded-md overflow-hidden border border-white/10"
                                data-image-id="{{ $image->id_product_image }}">
                                <img src="{{ Storage::url($image->image_path) }}" alt="{{ $product->name }}"
                                    class="w-full h-full object-cover object-center">
                                <button type="button"
                                    onclick="removeExistingImage(this, '{{ $editId }}', {{ $image->id_product_image }})"
                                    class="absolute -top-1 -right-1 bg-[#B71C1C] hover:bg-[#891212] text-white text-xs w-4 h-4 rounded-full flex items-center justify-center">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>

                    <div id="deleteImagesInputs-{{ $editId }}"></div>

                    <div>
                        <label class="block text-sm font-semibold mb-1.5 text-white">Tambah Foto Baru (opsional)</label>
                        <input type="file" id="editInputImages-{{ $editId }}" name="images[]" multiple
                            accept="image/*"
                            class="w-full bg-black border border-white/10 rounded-lg px-4 py-2.5 text-white file:mr-4 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:bg-[#B71C1C] file:text-white file:text-sm file:font-semibold">
                        <div id="editImageThumbs-{{ $editId }}" class="flex gap-2 flex-wrap mt-2"></div>
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

        // ---- Ukuran & Stok ----
        function removeEditVariantRow(button, editId) {
            const rows = document.querySelectorAll('.variant-row-' + editId);
            if (rows.length <= 1) {
                Swal.fire({
                    icon: 'info',
                    title: 'Tidak bisa dihapus',
                    text: 'Minimal harus ada satu ukuran & stok.',
                    confirmButtonColor: '#1C1CB7',
                });
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
                Swal.fire({
                    icon: 'info',
                    title: 'Semua ukuran sudah dipilih',
                    text: 'Ukuran S, M, L, XL sudah semuanya dipakai. Tidak ada ukuran tersisa untuk ditambahkan.',
                    confirmButtonColor: '#1C1CB7',
                });
                return;
            }

            const index = rows.length;
            let newRow;

            if (rows.length > 0) {
                newRow = rows[0].cloneNode(true);
            } else {
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

        // ---- Foto ----
        const editSelectedFiles = {}; // { editId: [file, file, ...] }

        function initEditImageHandlers(editId) {
            editSelectedFiles[editId] = [];

            const inputImages = document.getElementById('editInputImages-' + editId);
            if (!inputImages) return;

            inputImages.addEventListener('change', () => {
                const newFiles = Array.from(inputImages.files);
                editSelectedFiles[editId] = editSelectedFiles[editId].concat(newFiles);
                syncEditInputFiles(editId);
                renderEditImagePreview(editId);
            });
        }

        function renderEditImagePreview(editId) {
            const thumbsWrapper = document.getElementById('editImageThumbs-' + editId);
            thumbsWrapper.innerHTML = '';

            editSelectedFiles[editId].forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'relative';
                    wrapper.innerHTML = `
                        <img src="${e.target.result}" class="w-14 h-14 object-cover rounded-md border border-white/10">
                        <button type="button"
                            class="absolute -top-1 -right-1 bg-[#B71C1C] hover:bg-[#891212] text-white text-xs w-4 h-4 rounded-full flex items-center justify-center">
                            <i class="bi bi-x"></i>
                        </button>
                    `;
                    thumbsWrapper.appendChild(wrapper);

                    wrapper.querySelector('button').addEventListener('click', () => {
                        editSelectedFiles[editId].splice(index, 1);
                        syncEditInputFiles(editId);
                        renderEditImagePreview(editId);
                    });
                };
                reader.readAsDataURL(file);
            });
        }

        function syncEditInputFiles(editId) {
            const inputImages = document.getElementById('editInputImages-' + editId);
            const dataTransfer = new DataTransfer();
            editSelectedFiles[editId].forEach(file => dataTransfer.items.add(file));
            inputImages.files = dataTransfer.files;
        }

        function removeExistingImage(button, editId, imageId) {
            const wrapper = button.closest('.existing-image-' + editId);
            wrapper.remove();

            const hiddenInputsContainer = document.getElementById('deleteImagesInputs-' + editId);
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'delete_images[]';
            hiddenInput.value = imageId;
            hiddenInputsContainer.appendChild(hiddenInput);
        }

        function getRemainingImageCount(editId) {
            const existingCount = document.querySelectorAll('.existing-image-' + editId).length;
            const newCount = editSelectedFiles[editId] ? editSelectedFiles[editId].length : 0;
            return existingCount + newCount;
        }

        // ---- Init semua modal edit yang ada di halaman ----
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[id^="editForm-editModal-"]').forEach(form => {
                const editId = form.id.replace('editForm-', '');
                initEditImageHandlers(editId);

                form.addEventListener('submit', (e) => {
                    if (getRemainingImageCount(editId) < 1) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'warning',
                            title: 'Foto tidak boleh kosong',
                            text: 'Produk harus punya minimal 1 foto. Tambahkan foto baru atau jangan hapus semua foto lama.',
                            confirmButtonColor: '#B77B1C ',
                        });
                    }
                });
            });
        });
    </script>
@endonce
