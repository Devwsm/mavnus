@extends('template.dashboard.layout')
@section('content')
    <div class="flex flex-col items-center bg-black text-white w-full min-h-screen mb-24 z-20">
        @include('components/dashboard/navbar')

        <div class="flex flex-col w-full max-w-6xl gap-8 p-6 lg:p-14">
            <div>
                <h1 class="text-3xl font-bold uppercase">Input Produk</h1>
                <p class="text-white/50 text-sm mt-1">Pilih kategori dulu, field yang muncul otomatis nyesuain.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                {{-- ===================== FORM ===================== --}}
                <form action="{{ route('produk.store') }}" method="POST" enctype="multipart/form-data"
                    class="flex flex-col gap-6">
                    @csrf
                    @include('components/errors/alerts')

                    {{-- Kategori --}}
                    <div class="flex flex-col gap-4 bg-[#0D0D0D] border border-white/10 rounded-xl p-6">
                        <h2 class="text-xs font-semibold uppercase tracking-widest text-[#B71C1C]">Kategori</h2>
                        <x-dashboard.field.category-select selected="clothes" />
                    </div>

                    {{-- Data Dasar --}}
                    <div class="flex flex-col gap-4 bg-[#0D0D0D] border border-white/10 rounded-xl p-6">
                        <h2 class="text-xs font-semibold uppercase tracking-widest text-[#B71C1C]">Data Dasar</h2>
                        <x-dashboard.field.text-input label="Nama Produk" name="name" placeholder="misal: Yalla" />
                        <x-dashboard.field.text-input label="Harga" name="price" type="number" placeholder="250000" />
                        <x-dashboard.field.text-input label="Berat (gram)" name="weight" type="number"
                            placeholder="200" />
                        <x-dashboard.field.text-input label="Deskripsi" name="description"
                            placeholder="Deskripsi singkat produk" />
                    </div>

                    {{-- Jadwal Rilis --}}
                    <x-dashboard.field.release-schedule-toggle />

                    {{-- Detail Clothes (aktif default) --}}
                    <x-dashboard.field.clothes-fields :active="true" />
                    <x-dashboard.field.variant-rows :active="true" />

                    {{-- Detail Accessories (nonaktif default) --}}
                    <x-dashboard.field.accessories-fields :active="false" />

                    {{-- Foto --}}
                    <x-dashboard.field.image-upload />

                    <button type="submit"
                        class="bg-[#B71C1C] hover:bg-[#891212] text-white uppercase font-bold tracking-widest py-3 rounded-lg transition">
                        Simpan Produk
                    </button>
                </form>

                {{-- ===================== PREVIEW ===================== --}}
                <div class="lg:sticky lg:top-14 h-fit">
                    <h2 class="text-xs font-semibold uppercase tracking-widest text-white/40 mb-3">Preview Tampilan
                    </h2>
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
                        <div id="previewDetail" class="flex flex-col gap-2 pt-2 border-t border-white/10 mt-1">
                            <div class="flex justify-between">
                                <span class="text-white/40">Warna</span>
                                <span id="previewColor" class="text-white">-</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-white/40">Material</span>
                                <span id="previewMaterial" class="text-white">-</span>
                            </div>
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
        @if ($lowStockVariants->isNotEmpty() || $lowStockAccessories->isNotEmpty())
            <div class="flex flex-col w-full max-w-6xl gap-4 px-6 lg:px-14">
                <div class="bg-amber-400/5 border border-amber-400/20 rounded-2xl p-5 flex flex-col gap-4">
                    <div class="flex items-center gap-2.5">
                        <i class="bi bi-exclamation-triangle-fill text-amber-400"></i>
                        <h2 class="text-amber-400 text-sm font-bold uppercase tracking-wide">
                            Stok Menipis ({{ $lowStockVariants->count() + $lowStockAccessories->count() }})
                        </h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5">
                        @foreach ($lowStockVariants as $variant)
                            <a href="{{ route('dashboard.produk', ['edit' => optional($variant->product)->id_product]) }}#product-{{ optional($variant->product)->id_product }}"
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

                        @foreach ($lowStockAccessories as $accProduct)
                            <a href="{{ route('dashboard.produk', ['edit' => $accProduct->id_product]) }}#product-{{ $accProduct->id_product }}"
                                class="flex items-center gap-3 bg-black/40 hover:bg-black/60 border border-white/5 rounded-xl px-3.5 py-2.5 transition">
                                <div class="w-9 h-9 rounded-lg bg-white/5 overflow-hidden shrink-0">
                                    @if ($accProduct->images->first())
                                        <img src="{{ Storage::url($accProduct->images->first()->image_path) }}"
                                            alt="{{ $accProduct->name }}" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <div class="flex flex-col min-w-0">
                                    <span class="text-white text-xs font-semibold truncate">
                                        {{ $accProduct->name }}
                                    </span>
                                    <span class="text-white/40 text-[11px]">
                                        {{ optional($accProduct->accessories)->type_label ?? 'Accessory' }} · tersisa
                                        {{ $accProduct->stock }} pcs
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
                <h2 class="text-xl font-bold uppercase tracking-wide">Semua Produk</h2>
                <span class="text-white/40 text-sm">{{ $products->total() }} produk</span>
            </div>
            <div class="flex flex-col w-full">
                @include('components/errors/alerts')
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                @forelse ($products as $product)
                    @php
                        $isClothes = $product->category === 'clothes';
                        $totalStock = $isClothes ? $product->variants->sum('stock') : (int) $product->stock;
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

                            <div
                                class="absolute inset-x-0 bottom-0 h-20 bg-linear-to-t from-black/80 to-transparent pointer-events-none">
                            </div>

                            {{-- Badge kategori --}}
                            <div class="absolute top-3 left-3">
                                <span
                                    class="inline-flex items-center gap-1.5 bg-black/70 backdrop-blur text-white/70 text-[10px] font-semibold px-2 py-1 rounded-md capitalize">
                                    <i class="bi {{ $isClothes ? 'bi-bag-fill' : 'bi-gem' }}"></i>
                                    {{ $isClothes ? 'Clothes' : optional($product->accessories)->type_label ?? 'Accessories' }}
                                </span>
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

                            {{-- Strip mini thumbnail --}}
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

                        <div class="flex flex-col gap-2.5 p-4">
                            <div class="flex items-start justify-between gap-2">
                                <h3 class="text-white text-sm font-semibold leading-snug line-clamp-2">
                                    {{ $product->name }}</h3>
                                <span class="text-white text-sm font-bold shrink-0">{{ $product->formatted_price }}</span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-white/40 text-xs">Berat</span>
                                <span class="text-white text-sm">{{ $product->weight }} gram</span>
                            </div>

                            @if ($isClothes)
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
                            @else
                                @php
                                    $stockColor =
                                        $totalStock === 0
                                            ? 'text-[#e05656] border-[#B71C1C]/30 bg-[#B71C1C]/5'
                                            : ($totalStock <= 3
                                                ? 'text-amber-400 border-amber-400/20 bg-amber-400/5'
                                                : 'text-green-400 border-green-400/20 bg-green-400/5');
                                @endphp
                                <div class="flex flex-wrap gap-1.5">
                                    <span
                                        class="flex items-center gap-1 border rounded-md px-2 py-1 text-[11px] {{ $stockColor }}">
                                        <span class="font-medium opacity-70">Stok</span>
                                        <span class="opacity-30">·</span>
                                        <span class="font-semibold">{{ $totalStock }}</span>
                                    </span>
                                </div>
                            @endif

                            {{-- Aksi --}}
                            <div class="flex items-center gap-2 pt-3 border-t border-white/6">
                                @include('components/dashboard/modal-edit-produk')
                                @include('components/dashboard/btn-hapus-produk')
                            </div>
                        </div>
                    </div>
                @empty
                    <div
                        class="col-span-full flex flex-col items-center gap-2 py-20 bg-[#0D0D0D] border border-white/10 rounded-xl">
                        <i class="bi bi-inbox text-white/15 text-4xl"></i>
                        <p class="text-white/30 text-sm">Belum ada produk.</p>
                        <p class="text-white/20 text-xs">Isi form di atas untuk mulai menambahkan.</p>
                    </div>
                @endforelse
            </div>

            @if ($products->hasPages())
                <div class="mt-4">
                    {{ $products->links() }}
                </div>
            @endif
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
        // ---- Kategori awal: pastikan blok field sesuai default ("clothes") ----
        document.addEventListener('DOMContentLoaded', () => {
            mavnusToggleCategoryFields('');
        });

        // ---- Live text preview ----
        const inputName = document.getElementById('input-name');
        const inputPrice = document.getElementById('input-price');
        const inputWeight = document.getElementById('input-weight');
        const inputCategory = document.getElementById('inputCategory');

        const previewName = document.getElementById('previewName');
        const previewPrice = document.getElementById('previewPrice');
        const previewWeight = document.getElementById('previewWeight');
        const previewDetail = document.getElementById('previewDetail');

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

        function updateDetailPreview() {
            const category = inputCategory.value;
            if (category === 'clothes') {
                const color = document.getElementById('input-color')?.value || '-';
                const material = document.getElementById('input-material')?.value || '-';
                previewDetail.innerHTML = `
                    <div class="flex justify-between"><span class="text-white/40">Warna</span><span class="text-white">${color}</span></div>
                    <div class="flex justify-between"><span class="text-white/40">Material</span><span class="text-white">${material}</span></div>
                `;
                document.getElementById('previewVariants').closest('div.flex.flex-col.gap-1').style.display = '';
            } else {
                const typeSelect = document.getElementById('inputAccessoryType');
                const type = typeSelect ? typeSelect.options[typeSelect.selectedIndex].text : '-';
                const stock = document.getElementById('input-stock')?.value || '0';
                previewDetail.innerHTML = `
                    <div class="flex justify-between"><span class="text-white/40">Tipe</span><span class="text-white">${type}</span></div>
                    <div class="flex justify-between"><span class="text-white/40">Stok</span><span class="text-white">${stock} pcs</span></div>
                `;
                document.getElementById('previewVariants').closest('div.flex.flex-col.gap-1').style.display = 'none';
            }
        }

        inputCategory.addEventListener('change', updateDetailPreview);
        document.getElementById('input-color')?.addEventListener('input', updateDetailPreview);
        document.getElementById('input-material')?.addEventListener('input', updateDetailPreview);
        document.getElementById('inputAccessoryType')?.addEventListener('change', updateDetailPreview);
        document.getElementById('input-stock')?.addEventListener('input', updateDetailPreview);
        updateDetailPreview();

        // ---- Live preview Ukuran & Stok (clothes) ----
        const previewVariants = document.getElementById('previewVariants');

        function updateVariantPreview() {
            const rows = document.querySelectorAll('.variant-row');
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

        document.getElementById('variantRows').addEventListener('input', updateVariantPreview);
        document.getElementById('variantRows').addEventListener('change', updateVariantPreview);
        updateVariantPreview();
    </script>
@endsection
