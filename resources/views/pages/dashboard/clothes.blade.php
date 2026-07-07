@extends('template.dashboard.layout')
@section('content')
    <div class="flex flex-col items-center bg-black text-white w-full min-h-screen mb-24 z-20">
        @include('components/dashboard/navbar')

        <div class="flex flex-col w-full max-w-6xl gap-8 p-6 lg:p-14">
            <div>
                <h1 class="text-3xl font-bold uppercase">Tambah Produk — Clothes</h1>
                <p class="text-white/50 text-sm mt-1">Isi data di kiri, hasilnya bisa langsung dilihat di preview kanan.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                {{-- ===================== FORM ===================== --}}
                <form action="{{ route('clothes.store') }}" method="POST" enctype="multipart/form-data"
                    class="flex flex-col gap-6">
                    @csrf
                    {{-- Data Dasar --}}
                    <div class="flex flex-col gap-4 bg-[#0D0D0D] border border-white/10 rounded-xl p-6">
                        <h2 class="text-xs font-semibold uppercase tracking-widest text-[#B71C1C]">Data Dasar</h2>
                        <div>
                            <label class="block text-sm font-semibold mb-1.5">Nama Produk</label>
                            <input type="text" id="inputName" name="name"
                                class="w-full bg-black border border-white/10 rounded-lg px-4 py-2.5 text-white placeholder-white/30 focus:outline-none focus:border-[#B71C1C]"
                                placeholder="misal: Yalla">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1.5">Harga</label>
                            <input type="number" id="inputPrice" name="price"
                                class="w-full bg-black border border-white/10 rounded-lg px-4 py-2.5 text-white placeholder-white/30 focus:outline-none focus:border-[#B71C1C]"
                                placeholder="250000">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1.5">Deskripsi</label>
                            <textarea id="inputDescription" name="description" rows="3"
                                class="w-full bg-black border border-white/10 rounded-lg px-4 py-2.5 text-white placeholder-white/30 focus:outline-none focus:border-[#B71C1C]"
                                placeholder="Deskripsi singkat produk"></textarea>
                        </div>
                    </div>
                    {{-- Detail Clothes --}}
                    <div class="flex flex-col gap-4 bg-[#0D0D0D] border border-white/10 rounded-xl p-6">
                        <h2 class="text-xs font-semibold uppercase tracking-widest text-[#B71C1C]">Detail Clothes</h2>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold mb-1.5">Warna</label>
                                <input type="text" id="inputColor" name="color"
                                    class="w-full bg-black border border-white/10 rounded-lg px-4 py-2.5 text-white placeholder-white/30 focus:outline-none focus:border-[#B71C1C]"
                                    placeholder="Black">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-1.5">Material</label>
                                <input type="text" id="inputMaterial" name="material"
                                    class="w-full bg-black border border-white/10 rounded-lg px-4 py-2.5 text-white placeholder-white/30 focus:outline-none focus:border-[#B71C1C]"
                                    placeholder="Cotton Combed 24s">
                            </div>
                        </div>
                    </div>
                    {{-- Ukuran & Stok (dinamis) --}}
                    <div class="flex flex-col gap-4 bg-[#0D0D0D] border border-white/10 rounded-xl p-6">
                        <h2 class="text-xs font-semibold uppercase tracking-widest text-[#B71C1C]">Ukuran & Stok</h2>

                        <div id="variantRows" class="flex flex-col gap-3">
                            <div class="variant-row flex items-center gap-3">
                                <select name="variants[0][size]"
                                    class="variant-size min-w-0 bg-black border border-white/10 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-[#B71C1C]">
                                    <option value="S">S</option>
                                    <option value="M">M</option>
                                    <option value="L">L</option>
                                    <option value="XL">XL</option>
                                </select>
                                <input type="number" name="variants[0][stock]" placeholder="Stok"
                                    class="variant-stock min-w-0 flex-1 bg-black border border-white/10 rounded-lg px-4 py-2 text-white placeholder-white/30 focus:outline-none focus:border-[#B71C1C]">
                                <button type="button"
                                    class="removeVariantBtn text-white/40 hover:text-[#B71C1C] text-xl px-2 shrink-0">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                            </div>
                        </div>
                        <button type="button" id="addVariantBtn"
                            class="self-start text-sm font-semibold text-[#B71C1C] hover:text-[#891212] transition">
                            + Tambah Ukuran
                        </button>
                    </div>
                    {{-- Foto --}}
                    <div class="flex flex-col gap-4 bg-[#0D0D0D] border border-white/10 rounded-xl p-6">
                        <h2 class="text-xs font-semibold uppercase tracking-widest text-[#B71C1C]">Foto Produk</h2>
                        <input type="file" id="inputImages" name="images[]" multiple accept="image/*"
                            class="w-full bg-black border border-white/10 rounded-lg px-4 py-2.5 text-white file:mr-4 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:bg-[#B71C1C] file:text-white file:text-sm file:font-semibold">
                        <div id="imageThumbs" class="flex gap-2 flex-wrap"></div>
                    </div>
                    <button type="submit"
                        class="bg-[#B71C1C] hover:bg-[#891212] text-white uppercase font-bold tracking-widest py-3 rounded-lg transition">
                        Simpan Produk
                    </button>
                </form>

                {{-- ===================== PREVIEW ===================== --}}
                <div class="lg:sticky lg:top-14 h-fit">
                    <h2 class="text-xs font-semibold uppercase tracking-widest text-white/40 mb-3">Preview Tampilan</h2>
                    <div class="flex flex-col bg-black border border-white/10 gap-4 p-6 rounded-xl max-w-xs">
                        <div
                            class="w-full aspect-square overflow-hidden rounded-lg bg-[#0D0D0D] flex items-center justify-center">
                            <img id="previewImage" src="" alt="Preview"
                                class="w-full h-full object-cover object-center hidden">
                            <i id="previewImagePlaceholder" class="bi bi-image text-white/20 text-5xl"></i>
                        </div>
                        <div class="flex flex-col text-center gap-1">
                            <h3 id="previewName" class="text-sm font-semibold uppercase tracking-wide text-white">
                                Nama Produk
                            </h3>
                            <p id="previewPrice" class="text-sm text-white/70">Rp0</p>
                        </div>
                    </div>
                    <div class="bg-[#0D0D0D] border border-white/10 rounded-xl p-6 mt-4 flex flex-col gap-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-white/40">Warna</span>
                            <span id="previewColor" class="text-white">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-white/40">Material</span>
                            <span id="previewMaterial" class="text-white">-</span>
                        </div>
                        <div class="flex flex-col gap-1 pt-2 border-t border-white/10 mt-1">
                            <span class="text-white/40">Ukuran & Stok</span>
                            <div id="previewVariants" class="flex flex-col gap-1 text-white">
                                <span class="text-white/30">Belum diisi</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ---- Live text preview ----
        const inputName = document.getElementById('inputName');
        const inputPrice = document.getElementById('inputPrice');
        const inputColor = document.getElementById('inputColor');
        const inputMaterial = document.getElementById('inputMaterial');

        const previewName = document.getElementById('previewName');
        const previewPrice = document.getElementById('previewPrice');
        const previewColor = document.getElementById('previewColor');
        const previewMaterial = document.getElementById('previewMaterial');

        inputName.addEventListener('input', () => {
            previewName.textContent = inputName.value || 'Nama Produk';
        });

        inputPrice.addEventListener('input', () => {
            const value = parseInt(inputPrice.value || 0);
            previewPrice.textContent = 'Rp' + value.toLocaleString('id-ID');
        });

        inputColor.addEventListener('input', () => {
            previewColor.textContent = inputColor.value || '-';
        });

        inputMaterial.addEventListener('input', () => {
            previewMaterial.textContent = inputMaterial.value || '-';
        });

        // ---- Dynamic size & stock rows ----
        const variantRows = document.getElementById('variantRows');
        const addVariantBtn = document.getElementById('addVariantBtn');
        const previewVariants = document.getElementById('previewVariants');
        let variantIndex = 1;

        function updateVariantPreview() {
            const rows = variantRows.querySelectorAll('.variant-row');
            previewVariants.innerHTML = '';

            let hasData = false;
            rows.forEach(row => {
                const size = row.querySelector('.variant-size').value;
                const stock = row.querySelector('.variant-stock').value;
                if (stock) {
                    hasData = true;
                    const line = document.createElement('div');
                    line.className = 'flex justify-between';
                    line.innerHTML = `<span>${size}</span><span>${stock} pcs</span>`;
                    previewVariants.appendChild(line);
                }
            });

            if (!hasData) {
                previewVariants.innerHTML = '<span class="text-white/30">Belum diisi</span>';
            }
        }

        function bindVariantRow(row) {
            row.querySelector('.variant-size').addEventListener('change', updateVariantPreview);
            row.querySelector('.variant-stock').addEventListener('input', updateVariantPreview);
            row.querySelector('.removeVariantBtn').addEventListener('click', () => {
                if (variantRows.querySelectorAll('.variant-row').length > 1) {
                    row.remove();
                    updateVariantPreview();
                }
            });
        }

        bindVariantRow(variantRows.querySelector('.variant-row'));

        addVariantBtn.addEventListener('click', () => {
            const newRow = variantRows.querySelector('.variant-row').cloneNode(true);
            newRow.querySelector('.variant-size').name = `variants[${variantIndex}][size]`;
            newRow.querySelector('.variant-stock').name = `variants[${variantIndex}][stock]`;
            newRow.querySelector('.variant-stock').value = '';
            variantIndex++;
            variantRows.appendChild(newRow);
            bindVariantRow(newRow);
        });

        // ---- Image preview ----
        const inputImages = document.getElementById('inputImages');
        const previewImage = document.getElementById('previewImage');
        const previewImagePlaceholder = document.getElementById('previewImagePlaceholder');
        const imageThumbs = document.getElementById('imageThumbs');

        inputImages.addEventListener('change', () => {
            imageThumbs.innerHTML = '';
            const files = Array.from(inputImages.files);

            if (files.length === 0) {
                previewImage.classList.add('hidden');
                previewImagePlaceholder.classList.remove('hidden');
                return;
            }

            files.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    if (index === 0) {
                        previewImage.src = e.target.result;
                        previewImage.classList.remove('hidden');
                        previewImagePlaceholder.classList.add('hidden');
                    }

                    const thumb = document.createElement('img');
                    thumb.src = e.target.result;
                    thumb.className = 'w-16 h-16 object-cover rounded-md border border-white/10';
                    imageThumbs.appendChild(thumb);
                };
                reader.readAsDataURL(file);
            });
        });
    </script>
@endsection
