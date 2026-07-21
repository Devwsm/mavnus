@extends('template.layout')
@section('content')
    <div class="relative flex">
        @include('components/navbar')
    </div>
    <section class="flex flex-col w-full bg-white gap-10 p-6 lg:p-14 pt-28 md:pt-22 lg:pt-32">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold uppercase tracking-wide">Checkout</h1>
            <p class="text-sm text-gray-500 mt-2">Lengkapi data pengiriman untuk menyelesaikan pesanan.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Ringkasan cart --}}
            <div class="lg:col-span-1">
                <div class="lg:sticky lg:top-28 flex flex-col gap-4 border border-black/10 rounded-xl p-6">
                    <h2 class="text-sm font-bold uppercase tracking-widest text-black/50">Ringkasan Pesanan</h2>

                    <div class="flex flex-col gap-4 divide-y divide-black/5">
                        @foreach ($cartItems as $item)
                            <div class="flex gap-3 pt-4 first:pt-0">
                                <div
                                    class="w-14 h-14 rounded-lg overflow-hidden bg-gray-100 shrink-0 flex items-center justify-center">
                                    @if ($item->product->images->first())
                                        <img src="{{ Storage::url($item->product->images->first()->image_path) }}"
                                            class="w-full h-full object-cover object-center">
                                    @else
                                        <i class="bi bi-image text-gray-300"></i>
                                    @endif
                                </div>
                                <div class="flex flex-col flex-1 gap-0.5">
                                    <span class="text-sm font-semibold">{{ $item->product->name }}</span>
                                    @if ($item->variant)
                                        <span class="text-xs text-gray-500">Size: {{ $item->variant->label }}</span>
                                    @endif
                                    <div class="flex items-center justify-between text-sm mt-1">
                                        <span class="text-gray-500">{{ $item->quantity }}x</span>
                                        <span
                                            class="font-semibold">Rp{{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex flex-col gap-2 pt-4 border-t border-black/10">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Subtotal</span>
                            <span
                                class="font-semibold">Rp{{ number_format($cartItems->sum(fn($i) => $i->product->price * $i->quantity), 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Ongkir</span>
                            <span id="checkoutShippingDisplay" class="text-gray-400">Belum dipilih</span>
                        </div>
                        <div class="flex items-center justify-between text-base font-bold pt-2 border-t border-black/10">
                            <span>Total</span>
                            <span
                                id="checkoutTotalDisplay">Rp{{ number_format($cartItems->sum(fn($i) => $i->product->price * $i->quantity), 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form data customer --}}
            <form action="{{ route('order.store') }}" method="POST" class="lg:col-span-2 flex flex-col gap-6">
                @csrf
                <div class="flex flex-col gap-4 border border-black/10 rounded-xl p-6">
                    <h2 class="text-sm font-bold uppercase tracking-widest text-black/50">Data Penerima</h2>
                    <div>
                        <label class="block text-sm font-semibold mb-1.5">Nama Lengkap</label>
                        <input type="text" name="customer_name" value="{{ old('customer_name') }}"
                            class="w-full border border-black/10 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-black"
                            placeholder="Nama penerima">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1.5">Nomor HP</label>
                        <input type="text" name="customer_phone" value="{{ old('customer_phone') }}"
                            class="w-full border border-black/10 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-black"
                            placeholder="08xxxxxxxxxx">
                    </div>
                    <div class="relative">
                        <label class="block text-sm font-semibold mb-1.5">Kecamatan / Kota Tujuan</label>
                        <input type="text" id="destinationSearch" autocomplete="off"
                            class="w-full border border-black/10 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-black"
                            placeholder="Ketik nama kecamatan, kota, atau kode pos...">
                        <input type="hidden" name="destination_id" id="destinationId" value="{{ old('destination_id') }}">
                        <input type="hidden" name="destination_label" id="destinationLabel"
                            value="{{ old('destination_label') }}">
                        <div id="destinationResults"
                            class="hidden absolute top-full left-0 mt-1 w-full bg-white border border-black/10 rounded-lg shadow-lg max-h-60 overflow-y-auto z-20">
                        </div>
                        <p id="destinationSelected" class="text-xs text-gray-500 mt-1.5"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1.5">Alamat Detail</label>
                        <textarea name="customer_address" rows="3"
                            class="w-full border border-black/10 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-black"
                            placeholder="Nama jalan, nomor rumah, RT/RW, patokan">{{ old('customer_address') }}</textarea>
                    </div>
                </div>

                {{-- Pilihan kurir --}}
                <div id="shippingOptionsWrapper" class="hidden flex-col gap-3 border border-black/10 rounded-xl p-6">
                    <h2 class="text-sm font-bold uppercase tracking-widest text-black/50">Pilih Kurir</h2>
                    <div id="shippingOptionsList" class="flex flex-col gap-2"></div>
                </div>
                <input type="hidden" name="shipping_courier" id="shippingCourier">
                <input type="hidden" name="shipping_service" id="shippingService">
                <input type="hidden" name="shipping_cost" id="shippingCost" value="0">

                {{-- Placeholder payment — nanti diisi Midtrans --}}
                <div class="flex flex-col gap-2 border border-black/10 rounded-xl p-6 bg-gray-50">
                    <h2 class="text-sm font-bold uppercase tracking-widest text-black/50">Metode Pembayaran</h2>
                    <p class="text-sm text-gray-500">Pilihan pembayaran akan tersedia setelah fitur ini aktif.</p>
                </div>

                <button type="submit" id="checkoutSubmitBtn"
                    class="bg-black hover:bg-black/80 text-white uppercase font-bold tracking-widest py-3.5 rounded-lg transition">
                    Buat Pesanan
                </button>
            </form>

        </div>
    </section>

    <script>
        // ---- Search kecamatan/kota tujuan ----
        const destinationSearch = document.getElementById('destinationSearch');
        const destinationResults = document.getElementById('destinationResults');
        const destinationId = document.getElementById('destinationId');
        const destinationLabel = document.getElementById('destinationLabel');
        const destinationSelected = document.getElementById('destinationSelected');

        let destinationSearchTimeout = null;

        destinationSearch.addEventListener('input', () => {
            const keyword = destinationSearch.value.trim();

            clearTimeout(destinationSearchTimeout);
            destinationId.value = '';
            destinationLabel.value = '';
            destinationSelected.textContent = '';

            if (keyword.length < 3) {
                destinationResults.classList.add('hidden');
                return;
            }

            destinationSearchTimeout = setTimeout(() => {
                fetch(`{{ route('shipping.search') }}?keyword=${encodeURIComponent(keyword)}`)
                    .then(res => res.json())
                    .then(data => renderDestinationResults(data.data))
                    .catch(() => {
                        destinationResults.innerHTML =
                            '<p class="text-sm text-gray-400 p-3">Terjadi kesalahan, coba lagi.</p>';
                        destinationResults.classList.remove('hidden');
                    });
            }, 300);
        });

        function renderDestinationResults(results) {
            if (results.length === 0) {
                destinationResults.innerHTML = '<p class="text-sm text-gray-400 p-3">Tidak ditemukan.</p>';
                destinationResults.classList.remove('hidden');
                return;
            }

            destinationResults.innerHTML = results.map(item => `
                <button type="button"
                    class="destination-option w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50 border-b border-black/5 last:border-0"
                    data-id="${item.id}" data-label="${item.label}">
                    ${item.label}
                </button>
            `).join('');

            destinationResults.classList.remove('hidden');

            destinationResults.querySelectorAll('.destination-option').forEach(btn => {
                btn.addEventListener('click', () => {
                    destinationId.value = btn.dataset.id;
                    destinationLabel.value = btn.dataset.label;
                    destinationSearch.value = btn.dataset.label;
                    destinationSelected.textContent = `Terpilih: ${btn.dataset.label}`;
                    destinationResults.classList.add('hidden');

                    fetchShippingCost(btn.dataset.id);
                });
            });
        }

        document.addEventListener('click', (e) => {
            if (!destinationSearch.contains(e.target) && !destinationResults.contains(e.target)) {
                destinationResults.classList.add('hidden');
            }
        });

        // ---- Hitung ongkir setelah kota dipilih ----
        const shippingOptionsWrapper = document.getElementById('shippingOptionsWrapper');
        const shippingOptionsList = document.getElementById('shippingOptionsList');
        const shippingCourier = document.getElementById('shippingCourier');
        const shippingService = document.getElementById('shippingService');
        const shippingCost = document.getElementById('shippingCost');

        const cartTotalWeight = {{ $cartItems->sum(fn($i) => $i->product->weight * $i->quantity) }};
        const cartSubtotal = {{ $cartItems->sum(fn($i) => $i->product->price * $i->quantity) }};

        function fetchShippingCost(destinationIdValue) {
            shippingOptionsList.innerHTML = '<p class="text-sm text-gray-400">Menghitung ongkir...</p>';
            shippingOptionsWrapper.classList.remove('hidden');
            shippingOptionsWrapper.classList.add('flex');

            fetch('{{ route('shipping.cost') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        destination_id: destinationIdValue,
                        weight: cartTotalWeight,
                    }),
                })
                .then(res => res.json())
                .then(data => renderShippingOptions(data.data))
                .catch(() => {
                    shippingOptionsList.innerHTML =
                        '<p class="text-sm text-red-500">Gagal menghitung ongkir. Coba pilih ulang tujuan.</p>';
                });
        }

        function renderShippingOptions(options) {
            if (!options || options.length === 0) {
                shippingOptionsList.innerHTML =
                    '<p class="text-sm text-gray-400">Tidak ada layanan pengiriman untuk tujuan ini.</p>';
                return;
            }

            shippingOptionsList.innerHTML = options.map((opt, index) => `
                <label class="flex items-center justify-between gap-3 border border-black/10 rounded-lg px-4 py-3 cursor-pointer has-[:checked]:border-black has-[:checked]:bg-gray-50 transition">
                    <div class="flex items-center gap-3">
                        <input type="radio" name="shipping_option" value="${index}"
                            class="shipping-radio accent-black"
                            data-courier="${opt.name}" data-service="${opt.service}" data-cost="${opt.cost}">
                        <div class="flex flex-col">
                            <span class="text-sm font-semibold">${opt.name} - ${opt.service}</span>
                            <span class="text-xs text-gray-500">${opt.description}${opt.etd ? ' · ' + opt.etd : ''}</span>
                        </div>
                    </div>
                    <span class="text-sm font-bold">Rp${opt.cost.toLocaleString('id-ID')}</span>
                </label>
            `).join('');

            document.querySelectorAll('.shipping-radio').forEach(radio => {
                radio.addEventListener('change', () => {
                    shippingCourier.value = radio.dataset.courier;
                    shippingService.value = radio.dataset.service;
                    shippingCost.value = radio.dataset.cost;
                    updateCheckoutTotal();
                });
            });
        }

        function updateCheckoutTotal() {
            const cost = parseInt(shippingCost.value) || 0;
            const total = cartSubtotal + cost;

            document.getElementById('checkoutShippingDisplay').textContent = cost > 0 ? 'Rp' + cost.toLocaleString(
                'id-ID') : 'Belum dipilih';
            document.getElementById('checkoutTotalDisplay').textContent = 'Rp' + total.toLocaleString('id-ID');
        }

        // ---- Validasi sebelum submit ----
        document.querySelector('form[action="{{ route('order.store') }}"]').addEventListener('submit', (e) => {
            if (!destinationId.value) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Tujuan belum dipilih',
                    text: 'Silakan pilih kecamatan/kota tujuan terlebih dahulu.',
                    confirmButtonColor: '#B77B1C',
                });
                return;
            }
            if (!shippingCourier.value) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Kurir belum dipilih',
                    text: 'Silakan pilih kurir pengiriman terlebih dahulu.',
                    confirmButtonColor: '#B77B1C',
                });
            }
        });
    </script>

    @include('components/footer')
@endsection
