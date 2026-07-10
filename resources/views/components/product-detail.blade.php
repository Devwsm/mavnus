<section class="flex flex-col w-full bg-white max-w-7xl gap-24 p-6 lg:p-14 pt-22 md:pt-18 lg:pt-28">
    <div class="">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-10 lg:gap-16">
            <!-- Image gallery -->
            <div class="flex flex-col gap-4">
                <!-- Main image -->
                <button type="button" onclick="openGallery(0)"
                    class="relative w-full aspect-square bg-gray-100 overflow-hidden rounded-lg cursor-zoom-in group">
                    <img id="mainImage"
                        src="{{ $product->images->first() ? Storage::url($product->images->first()->image_path) : '' }}"
                        alt="{{ $product->name }}" class="absolute inset-0 w-full h-full object-contain" loading="eager"
                        fetchpriority="high" />
                    <span
                        class="absolute bottom-3 right-3 bg-black/70 text-white text-[10px] font-semibold px-2 py-1 rounded-md opacity-0 group-hover:opacity-100 transition">
                        <i class="bi bi-arrows-fullscreen"></i> Lihat semua foto
                    </span>
                </button>

                <!-- Sub Image -->
                @if ($product->images->count() > 1)
                    <div class="flex gap-4">
                        @foreach ($product->images->skip(1) as $index => $image)
                            <button type="button"
                                onclick="openGallery({{ $index }})"
                                class="relative w-20 h-20 bg-gray-100 rounded-lg overflow-hidden border border-transparent hover:border-gray-400">
                                <img src="{{ Storage::url($image->image_path) }}" alt="{{ $product->name }}"
                                    class="absolute inset-0 w-full h-full object-contain" />
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Product info -->
            <div class="flex flex-col gap-6">
                <!-- Title -->
                <h1 class="text-2xl md:text-3xl font-semibold tracking-tight"
                    style="color: {{ $product->clothes->color }}">
                    {{ $product->name }}
                </h1>
                <!-- Price -->
                <div class="flex items-baseline gap-3">
                    <span class="text-xl md:text-2xl font-semibold">{{ $product->formatted_price }}</span>
                </div>
                <!-- Description -->
                <p class="text-sm md:text-base text-gray-700 leading-relaxed">
                    {{ $product->description ?: 'Tidak ada deskripsi untuk produk ini.' }}
                </p>

                <!-- Materials -->
                <div class="text-xs text-gray-500 leading-relaxed">
                    <p class="mb-2">Warna: {{ $product->clothes->color }}</p>
                    <p>Material: {{ $product->clothes->material }}</p>
                </div>

                <!-- Sizes -->
                <div class="flex flex-col gap-3">
                    <span class="text-sm font-medium">Select Size</span>
                    <div class="flex flex-wrap gap-3">
                        @foreach ($product->clothes->variants as $variant)
                            <button type="button"
                                onclick="toggleSize('{{ $variant->size }}', {{ $variant->stock }}, {{ $variant->id_clothes_variant }})"
                                data-size="{{ $variant->size }}"
                                class="size-btn px-4 py-2 border border-gray-300 bg-white text-gray-900 rounded-lg text-sm hover:border-gray-500 {{ $variant->stock === 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                @disabled($variant->stock === 0)>
                                {{ $variant->size }}
                            </button>
                        @endforeach
                    </div>
                    <p id="size-message" class="text-xs text-red-600"></p>
                    <p id="stock-message" class="text-xs text-gray-500"></p>
                </div>
                <!-- Sizes End -->

                <!-- Quantity + Add to cart -->
                <div class="flex flex-col md:flex-row gap-4">
                    <div id="qty-wrapper" class="flex items-center justify-center w-fit border rounded-lg opacity-50">
                        <button id="qty-minus" type="button" disabled
                            class="px-3 py-2 text-gray-700 hover:text-black disabled:cursor-not-allowed">
                            -
                        </button>
                        <input id="qty-input" type="number" min="1" max="1" value="1" disabled
                            class="w-12 text-center outline-none disabled:cursor-not-allowed" />
                        <button id="qty-plus" type="button" disabled
                            class="px-3 py-2 text-gray-700 hover:text-black disabled:cursor-not-allowed">
                            +
                        </button>
                    </div>

                    <button id="addToCartBtn" type="button" disabled
                        class="px-6 py-2 bg-black text-white text-sm font-medium rounded-lg hover:bg-gray-800 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-black">
                        Pilih ukuran
                    </button>
                </div>

            </div>
        </div>
    </div>
</section>

{{-- Modal Galeri Foto --}}
@if ($product->images->count() > 0)
    <div id="galleryModal" class="hidden fixed inset-0 z-100 items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/90" onclick="closeGallery()"></div>

        <button type="button" onclick="closeGallery()"
            class="absolute top-5 right-5 text-white text-2xl w-10 h-10 flex items-center justify-center rounded-full hover:bg-white/10 transition z-10">
            <i class="bi bi-x-lg"></i>
        </button>

        @if ($product->images->count() > 1)
            <button type="button" onclick="navigateGallery(-1)"
                class="absolute left-3 md:left-8 top-1/2 -translate-y-1/2 text-white text-2xl w-10 h-10 flex items-center justify-center rounded-full hover:bg-white/10 transition z-10">
                <i class="bi bi-chevron-left"></i>
            </button>
            <button type="button" onclick="navigateGallery(1)"
                class="absolute right-3 md:right-8 top-1/2 -translate-y-1/2 text-white text-2xl w-10 h-10 flex items-center justify-center rounded-full hover:bg-white/10 transition z-10">
                <i class="bi bi-chevron-right"></i>
            </button>
        @endif

        <div class="relative w-full max-w-3xl aspect-square">
            <img id="galleryImage" src="" alt="{{ $product->name }}" class="w-full h-full object-contain">
        </div>

        @if ($product->images->count() > 1)
            <div class="absolute bottom-5 left-1/2 -translate-x-1/2 text-white/70 text-sm">
                <span id="galleryCounter"></span>
            </div>
        @endif
    </div>
@endif

<script>
    let selectedSize = null; // size yang sedang aktif dipilih, null kalau belum ada
    let selectedStock = 0; // stok dari size yang aktif, jadi batas atas qty
    let selectedVariantId = null; // id_clothes_variant dari size yang aktif, dikirim ke cart

    // Update tampilan visual tombol size (hitam = aktif, putih = tidak aktif)
    function updateSizeButtons() {
        const buttons = document.querySelectorAll('.size-btn');
        buttons.forEach(btn => {
            const size = btn.dataset.size;
            if (size === selectedSize) {
                btn.classList.remove('border-gray-300', 'bg-white', 'text-gray-900');
                btn.classList.add('border-black', 'bg-black', 'text-white');
            } else {
                if (!btn.disabled) {
                    btn.classList.remove('border-black', 'bg-black', 'text-white');
                    btn.classList.add('border-gray-300', 'bg-white', 'text-gray-900');
                }
            }
        });
    }

    // Pilih/batalkan size saat diklik (toggle)
    function toggleSize(size, stock, variantId) {
        if (stock === 0) {
            document.getElementById('size-message').textContent = 'Ukuran ini sedang tidak tersedia.';
            return;
        }

        // Klik size yang sudah aktif → batalkan pilihan
        if (selectedSize === size) {
            selectedSize = null;
            selectedStock = 0;
            selectedVariantId = null;
            document.getElementById('size-message').textContent = '';
            updateSizeButtons();
            setQtyEnabled(false);
            return;
        }

        // Klik size baru → pilih size itu
        selectedSize = size;
        selectedStock = stock;
        selectedVariantId = variantId;
        document.getElementById('size-message').textContent = '';
        document.getElementById('stock-message').textContent = `Stok tersedia: ${stock} pcs`;
        updateSizeButtons();
        setQtyEnabled(true);
    }

    // ---- Quantity control ----
    const qtyWrapper = document.getElementById('qty-wrapper');
    const qtyInput = document.getElementById('qty-input');
    const qtyMinus = document.getElementById('qty-minus');
    const qtyPlus = document.getElementById('qty-plus');
    const addToCartBtn = document.getElementById('addToCartBtn');

    function setQtyEnabled(enabled) {
        qtyInput.disabled = !enabled;
        qtyMinus.disabled = !enabled;
        qtyPlus.disabled = !enabled;
        addToCartBtn.disabled = !enabled;

        qtyWrapper.classList.toggle('opacity-50', !enabled);
        addToCartBtn.textContent = enabled ? 'Add to Cart' : 'Pilih ukuran dulu';

        if (enabled) {
            qtyInput.max = selectedStock;
            qtyInput.value = 1;
        } else {
            qtyInput.max = 1;
            qtyInput.value = 1;
            document.getElementById('stock-message').textContent = '';
        }

        updateQtyButtons();
    }

    function updateQtyButtons() {
        const value = parseInt(qtyInput.value, 10) || 1;
        const max = parseInt(qtyInput.max, 10) || 1;

        qtyMinus.disabled = qtyInput.disabled || value <= 1;
        qtyMinus.classList.toggle('opacity-50', qtyMinus.disabled);

        qtyPlus.disabled = qtyInput.disabled || value >= max;
        qtyPlus.classList.toggle('opacity-50', qtyPlus.disabled);
    }

    qtyMinus.addEventListener('click', () => {
        const value = parseInt(qtyInput.value, 10) || 1;
        if (value > 1) {
            qtyInput.value = value - 1;
        }
        updateQtyButtons();
    });

    qtyPlus.addEventListener('click', () => {
        const value = parseInt(qtyInput.value, 10) || 1;
        const max = parseInt(qtyInput.max, 10) || 1;
        if (value < max) {
            qtyInput.value = value + 1;
        }
        updateQtyButtons();
    });

    qtyInput.addEventListener('input', () => {
        const max = parseInt(qtyInput.max, 10) || 1;
        let value = parseInt(qtyInput.value, 10);

        if (isNaN(value) || value < 1) {
            value = 1;
        } else if (value > max) {
            value = max;
        }

        qtyInput.value = value;
        updateQtyButtons();
    });

    setQtyEnabled(false);

    // ---- Add to Cart ----
    addToCartBtn.addEventListener('click', () => {
        if (!selectedSize) return;

        fetch('{{ route('cart.add') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    product_id: {{ $product->id_product }},
                    clothes_variant_id: selectedVariantId,
                    quantity: parseInt(qtyInput.value, 10),
                }),
            })
            .then(res => res.json())
            .then(() => {
                openCart();
            });
    });

    // ---- Gallery modal ----
    const galleryImages = [
        @foreach ($product->images as $image)
            '{{ Storage::url($image->image_path) }}',
        @endforeach
    ];

    let currentGalleryIndex = 0;

    function openGallery(index) {
        currentGalleryIndex = index;
        renderGalleryImage();
        document.getElementById('galleryModal').classList.remove('hidden');
        document.getElementById('galleryModal').classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }

    function closeGallery() {
        document.getElementById('galleryModal').classList.add('hidden');
        document.getElementById('galleryModal').classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    }

    function navigateGallery(direction) {
        currentGalleryIndex = (currentGalleryIndex + direction + galleryImages.length) % galleryImages.length;
        renderGalleryImage();
    }

    function renderGalleryImage() {
        document.getElementById('galleryImage').src = galleryImages[currentGalleryIndex];

        const counter = document.getElementById('galleryCounter');
        if (counter) {
            counter.textContent = `${currentGalleryIndex + 1} / ${galleryImages.length}`;
        }
    }

    document.addEventListener('keydown', (e) => {
        const modal = document.getElementById('galleryModal');
        if (modal.classList.contains('hidden')) return;

        if (e.key === 'Escape') closeGallery();
        if (e.key === 'ArrowLeft') navigateGallery(-1);
        if (e.key === 'ArrowRight') navigateGallery(1);
    });
</script>
