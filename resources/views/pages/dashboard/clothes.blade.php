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
                    @include('components/errors/alerts')
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
                            <label class="block text-sm font-semibold mb-1.5">Berat (gram)</label>
                            <input type="number" id="inputWeight" name="weight"
                                class="w-full bg-black border border-white/10 rounded-lg px-4 py-2.5 text-white placeholder-white/30 focus:outline-none focus:border-[#B71C1C]"
                                placeholder="200">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1.5">Deskripsi</label>
                            <input type="text" id="inputDescription" name="description"
                                class="w-full bg-black border border-white/10 rounded-lg px-4 py-2.5 text-white placeholder-white/30 focus:outline-none focus:border-[#B71C1C]"
                                placeholder="Deskripsi singkat produk"></input>
                        </div>
                    </div>
                    {{-- Jadwal Rilis --}}
                    <div class="flex flex-col gap-4 bg-[#0D0D0D] border border-white/10 rounded-xl p-6">
                        <h2 class="text-xs font-semibold uppercase tracking-widest text-[#B71C1C]">Jadwal Rilis</h2>

                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="release_mode" value="now" id="releaseModeNow" checked
                                    class="accent-[#B71C1C]">
                                <span class="text-sm text-white">Publish Sekarang</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="release_mode" value="scheduled" id="releaseModeScheduled"
                                    class="accent-[#B71C1C]">
                                <span class="text-sm text-white">Jadwalkan</span>
                            </label>
                        </div>

                        <div id="scheduledAtWrapper" style="display: none;">
                            @include('components.dashboard.release-schedule-picker', ['id' => 'create'])
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
                                    placeholder="Black / Blue....">
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
                                <input type="number" name="variants[0][stock]" placeholder="Stock"
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
                            <span class="text-white/40">Berat (gram)</span>
                            <span id="previewWeight" class="text-white">-</span>
                        </div>
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

        {{-- ===================== STOK MENIPIS ===================== --}}
        @if ($lowStockVariants->isNotEmpty())
            <div class="flex flex-col w-full max-w-6xl gap-4 px-6 lg:px-14">
                <div class="bg-amber-400/5 border border-amber-400/20 rounded-2xl p-5 flex flex-col gap-4">
                    <div class="flex items-center gap-2.5">
                        <i class="bi bi-exclamation-triangle-fill text-amber-400"></i>
                        <h2 class="text-amber-400 text-sm font-bold uppercase tracking-wide">
                            Stok Menipis ({{ $lowStockVariants->count() }})
                        </h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5">
                        @foreach ($lowStockVariants as $variant)
                            <a href="{{ route('dashboard.clothes', ['edit' => optional($variant->product)->id_product]) }}#product-{{ optional($variant->product)->id_product }}"
                                class="flex items-center gap-3 bg-black/40 hover:bg-black/60 border border-white/5 rounded-xl px-3.5 py-2.5 transition">
                                <div class="w-9 h-9 rounded-lg bg-white/5 overflow-hidden shrink-0">
                                    @if (optional($variant->product)->images && $variant->product->images->first())
                                        <img src="{{ Storage::url($variant->product->images->first()->image_path) }}"
                                            alt="{{ $variant->product->name }}" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <div class="flex flex-col min-w-0">
                                    <span class="text-white text-xs font-semibold truncate">
                                        {{ $variant->product->name ?? 'Produk tidak ditemukan' }}
                                    </span>
                                    <span class="text-white/40 text-[11px]">
                                        Ukuran {{ $variant->label }} · tersisa {{ $variant->stock }} pcs
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- ===================== SEMUA PRODUK (edit/hapus) ===================== --}}
        <div class="flex flex-col w-full max-w-6xl gap-4 p-6 lg:p-14">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold uppercase tracking-wide">Semua Produk Clothes</h2>
                <span class="text-white/40 text-sm">{{ $products->count() }} produk</span>
            </div>
            <div class="flex flex-col w-full">
                @include('components/errors/alerts')
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                @forelse ($products as $product)
                    @php
                        $totalStock = $product->variants->sum('stock');
                        $extraImages = $product->images->slice(1, 3);
                        $remainingCount = $product->images->count() - 4;
                    @endphp
                    <div id="product-{{ $product->id_product }}"
                        class="group bg-[#0D0D0D] border border-white/10 rounded-2xl overflow-hidden hover:border-white/20 transition-all duration-300 scroll-mt-24">

                        {{-- Foto --}}
                        <div class="relative w-full aspect-square bg-black overflow-hidden">
                            @if ($product->images->first())
                                <img src="{{ Storage::url($product->images->first()->image_path) }}"
                                    alt="{{ $product->name }}"
                                    class="w-full h-full object-cover object-center group-hover:scale-105 transition duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="bi bi-image text-white/15 text-4xl"></i>
                                </div>
                            @endif

                            {{-- Gradient overlay biar strip foto & badge lebih nempel --}}
                            <div
                                class="absolute inset-x-0 bottom-0 h-20 bg-linear-to-t from-black/80 to-transparent pointer-events-none">
                            </div>

                            {{-- Status badge --}}
                            <div class="absolute top-3 right-3 flex flex-col items-end gap-1.5">
                                @if ($product->is_scheduled)
                                    <span
                                        class="inline-flex items-center gap-1.5 bg-black/70 backdrop-blur text-amber-400 text-[10px] font-semibold px-2 py-1 rounded-md">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                        Terjadwal {{ $product->published_at->translatedFormat('d M, H:i') }}
                                    </span>
                                @elseif ($product->is_active)
                                    <span
                                        class="inline-flex items-center gap-1.5 bg-black/70 backdrop-blur text-green-400 text-[10px] font-semibold px-2 py-1 rounded-md">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-400"></span>
                                        Tersedia
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1.5 bg-black/70 backdrop-blur text-[#e05656] text-[10px] font-semibold px-2 py-1 rounded-md">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#e05656]"></span>
                                        Sold Out
                                    </span>
                                @endif
                            </div>

                            {{-- Strip mini thumbnail (kalau foto lebih dari 1) --}}
                            @if ($product->images->count() > 1)
                                <div class="absolute bottom-3 left-3 flex items-center gap-1.5">
                                    @foreach ($extraImages as $extra)
                                        <div class="w-7 h-7 rounded-md overflow-hidden ring-1.5 ring-white/40">
                                            <img src="{{ Storage::url($extra->image_path) }}" alt="{{ $product->name }}"
                                                class="w-full h-full object-cover object-center">
                                        </div>
                                    @endforeach

                                    @if ($remainingCount > 0)
                                        <div class="w-7 h-7 rounded-md bg-[#B71C1C] flex items-center justify-center">
                                            <span class="text-white text-[10px] font-bold">+{{ $remainingCount }}</span>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="flex flex-col gap-3 p-4">
                            <div>
                                <h3 class="font-semibold text-white uppercase tracking-wide truncate">
                                    {{ $product->name }}
                                </h3>
                                <p class="text-white/40 text-xs mt-0.5 line-clamp-1">
                                    {{ $product->description ?: 'Tidak ada deskripsi' }}
                                </p>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-white font-bold">{{ $product->formatted_price }}</span>
                                <div class="flex items-center gap-1.5">
                                    <span class="w-3 h-3 rounded-full border border-white/20"
                                        style="background-color: {{ $product->clothes->color }}"></span>
                                    <span class="text-white/50 text-xs">{{ $product->clothes->material }}</span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-3 border-t border-white/6">
                                <span class="text-white/40 text-xs">Total Stok</span>
                                <span class="text-white text-sm font-semibold">{{ $totalStock }} pcs</span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-white/40 text-xs">Berat</span>
                                <span class="text-white text-sm">{{ $product->weight }} gram</span>
                            </div>

                            <div class="flex flex-wrap gap-1.5">
                                @foreach ($product->variants as $variant)
                                    @php
                                        $stockColor =
                                            $variant->stock === 0
                                                ? 'text-[#e05656] border-[#B71C1C]/30 bg-[#B71C1C]/5'
                                                : ($variant->stock <= 3
                                                    ? 'text-amber-400 border-amber-400/20 bg-amber-400/5'
                                                    : 'text-green-400 border-green-400/20 bg-green-400/5');
                                    @endphp
                                    <span
                                        class="flex items-center gap-1 border rounded-md px-2 py-1 text-[11px] {{ $stockColor }}">
                                        <span class="font-medium opacity-70">{{ $variant->label }}</span>
                                        <span class="opacity-30">·</span>
                                        <span class="font-semibold">{{ $variant->stock }}</span>
                                    </span>
                                @endforeach
                            </div>

                            {{-- Aksi --}}
                            <div class="flex items-center gap-2 pt-3 border-t border-white/6">
                                @include('components/dashboard/modal-edit-clothes')
                                @include('components/dashboard/btn-hapus-clothes')
                            </div>
                        </div>
                    </div>
                @empty
                    <div
                        class="col-span-full flex flex-col items-center gap-2 py-20 bg-[#0D0D0D] border border-white/10 rounded-xl">
                        <i class="bi bi-inbox text-white/15 text-4xl"></i>
                        <p class="text-white/30 text-sm">Belum ada produk clothes.</p>
                        <p class="text-white/20 text-xs">Isi form di atas untuk mulai menambahkan.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        // Kalau datang dari klik "Stok Menipis" (?edit=ID), otomatis buka modal edit produk itu
        document.addEventListener('DOMContentLoaded', () => {
            const editId = new URLSearchParams(window.location.search).get('edit');
            if (!editId) return;

            const card = document.getElementById('product-' + editId);
            if (card) {
                card.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }

            const modal = document.getElementById('editModal-' + editId);
            if (modal && typeof toggleEditModal === 'function') {
                toggleEditModal('editModal-' + editId, true);
            }
        });
    </script>

    <script>
        // ---- Toggle field jadwal rilis ----
        const releaseModeNow = document.getElementById('releaseModeNow');
        const releaseModeScheduled = document.getElementById('releaseModeScheduled');
        const scheduledAtWrapper = document.getElementById('scheduledAtWrapper');

        function toggleScheduledField() {
            const isScheduled = releaseModeScheduled.checked;
            scheduledAtWrapper.style.display = isScheduled ? 'block' : 'none';
            if (isScheduled) {
                mavnusInitReleasePicker('create', {{ now()->format('H') }},
                    {{ (int) (round(now()->format('i') / 5) * 5) % 60 }});
            }
        }

        releaseModeNow.addEventListener('change', toggleScheduledField);
        releaseModeScheduled.addEventListener('change', toggleScheduledField);

        // ---- Live text preview ----
        const inputName = document.getElementById('inputName');
        const inputPrice = document.getElementById('inputPrice');
        const inputColor = document.getElementById('inputColor');
        const inputWeight = document.getElementById('inputWeight');
        const inputMaterial = document.getElementById('inputMaterial');

        const previewName = document.getElementById('previewName');
        const previewPrice = document.getElementById('previewPrice');
        const previewColor = document.getElementById('previewColor');
        const previewWeight = document.getElementById('previewWeight');
        const previewMaterial = document.getElementById('previewMaterial');

        inputName.addEventListener('input', () => {
            previewName.textContent = inputName.value || 'Nama Produk';
        });

        inputPrice.addEventListener('input', () => {
            const value = parseInt(inputPrice.value || 0);
            previewPrice.textContent = 'Rp.' + value.toLocaleString('id-ID');
        });

        inputWeight.addEventListener('input', () => {
            previewWeight.textContent = inputWeight.value || '-';
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

        function updateSizeAvailability() {
            const rows = variantRows.querySelectorAll('.variant-row');
            const selectedSizes = Array.from(rows).map(row => row.querySelector('.variant-size').value);

            rows.forEach(row => {
                const select = row.querySelector('.variant-size');
                Array.from(select.options).forEach(option => {
                    const isUsedElsewhere = selectedSizes.includes(option.value) && option.value !== select
                        .value;
                    option.disabled = isUsedElsewhere;
                });
            });
        }

        function getNextAvailableSize(excludeSelect) {
            const rows = variantRows.querySelectorAll('.variant-row');
            const usedSizes = Array.from(rows)
                .map(row => row.querySelector('.variant-size'))
                .filter(select => select !== excludeSelect)
                .map(select => select.value);

            const allSizes = ['S', 'M', 'L', 'XL'];
            return allSizes.find(size => !usedSizes.includes(size)) || '';
        }

        function bindVariantRow(row) {
            row.querySelector('.variant-size').addEventListener('change', () => {
                updateVariantPreview();
                updateSizeAvailability();
            });
            row.querySelector('.variant-stock').addEventListener('input', updateVariantPreview);
            row.querySelector('.removeVariantBtn').addEventListener('click', () => {
                if (variantRows.querySelectorAll('.variant-row').length > 1) {
                    row.remove();
                    updateVariantPreview();
                    updateSizeAvailability();
                }
            });
        }

        bindVariantRow(variantRows.querySelector('.variant-row'));
        updateSizeAvailability();

        addVariantBtn.addEventListener('click', () => {
            const nextSize = getNextAvailableSize(null);

            if (!nextSize) {
                Swal.fire({
                    icon: 'info',
                    title: 'Semua ukuran sudah dipilih',
                    text: 'Ukuran S, M, L, XL sudah semuanya dipakai. Tidak ada ukuran tersisa untuk ditambahkan.',
                    confirmButtonColor: '#1C1CB7 ',
                });
                return;
            }

            const newRow = variantRows.querySelector('.variant-row').cloneNode(true);
            const newSelect = newRow.querySelector('.variant-size');

            newSelect.name = `variants[${variantIndex}][size]`;
            newSelect.value = nextSize;
            newRow.querySelector('.variant-stock').name = `variants[${variantIndex}][stock]`;
            newRow.querySelector('.variant-stock').value = '';
            variantIndex++;

            variantRows.appendChild(newRow);

            bindVariantRow(newRow);
            updateSizeAvailability();
        });

        // ---- Image preview (akumulatif, bisa ditambah satu-satu) ----
        const inputImages = document.getElementById('inputImages');
        const previewImage = document.getElementById('previewImage');
        const previewImagePlaceholder = document.getElementById('previewImagePlaceholder');
        const imageThumbs = document.getElementById('imageThumbs');

        let selectedFiles = [];

        function renderImagePreview() {
            imageThumbs.innerHTML = '';

            if (selectedFiles.length === 0) {
                previewImage.classList.add('hidden');
                previewImagePlaceholder.classList.remove('hidden');
                return;
            }

            selectedFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    if (index === 0) {
                        previewImage.src = e.target.result;
                        previewImage.classList.remove('hidden');
                        previewImagePlaceholder.classList.add('hidden');
                    }

                    const wrapper = document.createElement('div');
                    wrapper.className = 'relative';
                    wrapper.innerHTML = `
                    <img src="${e.target.result}" class="w-16 h-16 object-cover rounded-md border border-white/10">
                    <button type="button" data-index="${index}"
                        class="removeImageBtn absolute -top-1.5 -right-1.5 bg-[#B71C1C] hover:bg-[#891212] text-white text-xs w-5 h-5 rounded-full flex items-center justify-center">
                        <i class="bi bi-x"></i>
                    </button>
                `;
                    imageThumbs.appendChild(wrapper);

                    wrapper.querySelector('.removeImageBtn').addEventListener('click', () => {
                        selectedFiles.splice(index, 1);
                        syncInputFiles();
                        renderImagePreview();
                    });
                };
                reader.readAsDataURL(file);
            });
        }

        function syncInputFiles() {
            const dataTransfer = new DataTransfer();
            selectedFiles.forEach(file => dataTransfer.items.add(file));
            inputImages.files = dataTransfer.files;
        }

        inputImages.addEventListener('change', () => {
            const newFiles = Array.from(inputImages.files);
            selectedFiles = selectedFiles.concat(newFiles);
            syncInputFiles();
            renderImagePreview();
        });
    </script>
@endsection
