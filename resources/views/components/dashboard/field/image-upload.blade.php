{{--
    Upload foto produk + preview thumbnail. Buat mode edit, foto yang udah
    ada ditampilin duluan dengan tombol hapus (submit ke delete_images[]).

    Props:
    - suffix         : id scoping (form create = '', modal edit = '-editModal-{id}')
    - existingImages : koleksi ProductImage (buat edit), null buat create
--}}
@props([
    'suffix' => '',
    'existingImages' => null,
])

<div class="flex flex-col gap-4 bg-[#0D0D0D] border border-white/10 rounded-xl p-6">
    <h2 class="text-xs font-semibold uppercase tracking-widest text-[#B71C1C]">Foto Produk</h2>

    @if ($existingImages && $existingImages->isNotEmpty())
        <div>
            <p class="text-white/40 text-xs mb-2">Foto Saat Ini</p>
            <div id="existingImages{{ $suffix }}" class="flex flex-wrap gap-2">
                @foreach ($existingImages as $image)
                    <div class="existing-image{{ $suffix }} relative">
                        <img src="{{ Storage::url($image->image_path) }}"
                            class="w-14 h-14 object-cover rounded-md border border-white/10">
                        <button type="button"
                            onclick="mavnusRemoveExistingImage(this, '{{ $suffix }}', {{ $image->id_product_image }})"
                            class="absolute -top-1.5 -right-1.5 bg-[#B71C1C] hover:bg-[#891212] text-white text-xs w-5 h-5 rounded-full flex items-center justify-center">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                @endforeach
            </div>
            <div id="deleteImagesInputs{{ $suffix }}"></div>
        </div>
    @endif

    <input type="file" id="inputImages{{ $suffix }}" name="images[]" multiple accept="image/*"
        class="w-full bg-black border border-white/10 rounded-lg px-4 py-2.5 text-white file:mr-4 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:bg-[#B71C1C] file:text-white file:text-sm file:font-semibold">
    <div id="imageThumbs{{ $suffix }}" class="flex gap-2 flex-wrap"></div>
</div>

@once
    <script>
        // ---- Upload foto (generic, dipakai form create + semua modal edit) ----
        const mavnusSelectedFiles = {};

        function mavnusInitImageUpload(suffix) {
            const input = document.getElementById('inputImages' + suffix);
            if (!input || input.dataset.mavnusInit) return;
            input.dataset.mavnusInit = '1';
            mavnusSelectedFiles[suffix] = [];

            input.addEventListener('change', () => {
                const newFiles = Array.from(input.files);
                mavnusSelectedFiles[suffix] = mavnusSelectedFiles[suffix].concat(newFiles);
                mavnusSyncImageInput(suffix);
                mavnusRenderImagePreview(suffix);
            });
        }

        function mavnusSyncImageInput(suffix) {
            const input = document.getElementById('inputImages' + suffix);
            const dataTransfer = new DataTransfer();
            mavnusSelectedFiles[suffix].forEach(file => dataTransfer.items.add(file));
            input.files = dataTransfer.files;
        }

        function mavnusRenderImagePreview(suffix) {
            const thumbs = document.getElementById('imageThumbs' + suffix);
            thumbs.innerHTML = '';

            mavnusSelectedFiles[suffix].forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'relative';
                    wrapper.innerHTML = `
                        <img src="${e.target.result}" class="w-16 h-16 object-cover rounded-md border border-white/10">
                        <button type="button" class="removeImageBtn absolute -top-1.5 -right-1.5 bg-[#B71C1C] hover:bg-[#891212] text-white text-xs w-5 h-5 rounded-full flex items-center justify-center">
                            <i class="bi bi-x"></i>
                        </button>
                    `;
                    thumbs.appendChild(wrapper);

                    wrapper.querySelector('.removeImageBtn').addEventListener('click', () => {
                        mavnusSelectedFiles[suffix].splice(index, 1);
                        mavnusSyncImageInput(suffix);
                        mavnusRenderImagePreview(suffix);
                    });

                    // Preview panel cuma ada di form create (suffix kosong)
                    if (suffix === '' && index === 0) {
                        const previewImage = document.getElementById('previewImage');
                        const previewImagePlaceholder = document.getElementById('previewImagePlaceholder');
                        if (previewImage && previewImagePlaceholder) {
                            previewImage.src = e.target.result;
                            previewImage.classList.remove('hidden');
                            previewImagePlaceholder.classList.add('hidden');
                        }
                    }
                };
                reader.readAsDataURL(file);
            });

            if (suffix === '' && mavnusSelectedFiles[suffix].length === 0) {
                const previewImage = document.getElementById('previewImage');
                const previewImagePlaceholder = document.getElementById('previewImagePlaceholder');
                if (previewImage && previewImagePlaceholder) {
                    previewImage.classList.add('hidden');
                    previewImagePlaceholder.classList.remove('hidden');
                }
            }
        }

        function mavnusRemoveExistingImage(button, suffix, imageId) {
            const wrapper = button.closest('.existing-image' + suffix);
            wrapper.remove();

            const hiddenContainer = document.getElementById('deleteImagesInputs' + suffix);
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'delete_images[]';
            hidden.value = imageId;
            hiddenContainer.appendChild(hidden);
        }

        function mavnusRemainingImageCount(suffix) {
            const existingCount = document.querySelectorAll('.existing-image' + suffix).length;
            const newCount = mavnusSelectedFiles[suffix] ? mavnusSelectedFiles[suffix].length : 0;
            return existingCount + newCount;
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('input[id^="inputImages"]').forEach(input => {
                const suffix = input.id.replace('inputImages', '');
                mavnusInitImageUpload(suffix);
            });

            // Validasi ringan: form edit gak boleh disubmit tanpa foto sama sekali
            document.querySelectorAll('form[id^="editForm-"]').forEach(form => {
                const suffix = '-' + form.id.replace('editForm-editModal-', 'editModal-');
                form.addEventListener('submit', (e) => {
                    if (mavnusRemainingImageCount(suffix) < 1) {
                        e.preventDefault();
                        if (window.Swal) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Foto tidak boleh kosong',
                                text: 'Produk harus punya minimal 1 foto. Tambahkan foto baru atau jangan hapus semua foto lama.',
                                confirmButtonColor: '#B77B1C',
                            });
                        }
                    }
                });
            });
        });
    </script>
@endonce
