{{--
    Modal Edit — 1 komponen buat clothes & accessories, field yang muncul
    otomatis nyesuain $product->category (kategori gak bisa diganti di sini).
    Semua field detail dipakai dari components/dashboard/field/...
--}}
@php
    $editId = 'editModal-' . $product->id_product;
    $suffix = '-' . $editId;
    $isClothes = $product->category === 'clothes';
@endphp

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

    <div
        class="relative bg-[#0D0D0D] border border-white/10 rounded-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto no-scrollbar">

        <div class="flex items-center justify-between p-6 border-b border-white/10 sticky top-0 bg-[#0D0D0D] z-10">
            <h2 class="text-lg font-bold text-white uppercase tracking-wide">Edit {{ $product->name }}</h2>
            <button type="button" onclick="toggleEditModal('{{ $editId }}', false)"
                class="text-white/40 hover:text-white text-xl">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form id="editForm-{{ $editId }}" action="{{ route('produk.update', $product) }}" method="POST"
            enctype="multipart/form-data" class="flex flex-col gap-6 p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Data Dasar --}}
                <div class="flex flex-col gap-4">
                    <h3 class="text-xs font-semibold uppercase tracking-widest text-[#B71C1C]">Data Dasar</h3>

                    <x-dashboard.field.category-select :selected="$product->category" :suffix="$suffix" :disabled="true" />

                    <x-dashboard.field.text-input label="Nama Produk" name="name" :value="$product->name"
                        :suffix="$suffix" />
                    <x-dashboard.field.text-input label="Harga" name="price" type="number" :value="$product->price"
                        :suffix="$suffix" />
                    <x-dashboard.field.text-input label="Berat (gram)" name="weight" type="number" :value="$product->weight"
                        :suffix="$suffix" />
                    <x-dashboard.field.text-input label="Deskripsi" name="description" :value="$product->description"
                        :suffix="$suffix" />
                </div>

                {{-- Detail sesuai kategori (kategori fix, jadi cukup render 1 blok) --}}
                @if ($isClothes)
                    <x-dashboard.field.clothes-fields :suffix="$suffix" :color="$product->clothes->color" :material="$product->clothes->material"
                        :active="true" :boxed="false" />
                @else
                    <x-dashboard.field.accessories-fields :suffix="$suffix" :type="$product->accessories->type" :stock="$product->stock"
                        :active="true" :boxed="false" />
                @endif
            </div>

            {{-- Jadwal Rilis --}}
            <x-dashboard.field.release-schedule-toggle :suffix="$suffix" :scheduled="$product->is_scheduled" :date="$product->is_scheduled ? $product->published_at->format('Y-m-d') : null"
                :hour="$product->is_scheduled ? (int) $product->published_at->format('H') : null" :minute="$product->is_scheduled
                    ? (int) (round($product->published_at->format('i') / 5) * 5) % 60
                    : null" :defaultHour="$product->is_scheduled
                    ? (int) $product->published_at->format('H')
                    : (int) now()->format('H')" :defaultMinute="$product->is_scheduled
                    ? (int) (round($product->published_at->format('i') / 5) * 5) % 60
                    : (int) (round(now()->format('i') / 5) * 5) % 60" />

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Ukuran & Stok (clothes only) --}}
                @if ($isClothes)
                    <x-dashboard.field.variant-rows :suffix="$suffix" :variants="$product->variants
                        ->map(fn($v) => ['size' => $v->label, 'stock' => $v->stock])
                        ->toArray()" :active="true" />
                @endif

                {{-- Foto --}}
                <x-dashboard.field.image-upload :suffix="$suffix" :existingImages="$product->images" />
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

            // Kalau produk ini emang udah dijadwalkan, init wheel picker pas modal
            // beneran kebuka (gak bisa init pas modal masih display:none)
            if (show) {
                const suffix = '-' + editId;
                const scheduledRadio = document.getElementById('releaseModeScheduled' + suffix);
                if (scheduledRadio?.checked) {
                    const wrapper = document.getElementById('scheduledAtWrapper' + suffix);
                    mavnusInitReleasePicker(editId, Number(wrapper.dataset.defaultHour), Number(wrapper.dataset
                        .defaultMinute));
                }
            }
        }
    </script>
@endonce
