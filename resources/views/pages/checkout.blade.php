@extends('template.layout')
@section('content')
    <section id="main-content" class="flex flex-col w-full bg-white gap-10 p-6 lg:p-14 pt-28 md:pt-22 lg:pt-32">
        @include('components/errors/alerts')
        <div>
            <h1 class="text-2xl md:text-3xl font-bold uppercase tracking-wide">{{ __('checkout.heading') }}</h1>
            <p class="text-sm text-gray-500 mt-2">{{ __('checkout.subheading') }}</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Ringkasan cart --}}
            <div class="lg:col-span-1">
                <div class="lg:sticky lg:top-28 flex flex-col gap-4 border border-black/10 rounded-xl p-6">
                    <h2 class="text-sm font-bold uppercase tracking-widest text-black/50">{{ __('checkout.summary') }}</h2>

                    <div class="flex flex-col gap-4 divide-y divide-black/5">
                        @foreach ($cartItems as $item)
                            <div class="flex gap-3 pt-4 first:pt-0">
                                <div
                                    class="w-14 h-14 rounded-lg overflow-hidden bg-gray-100 shrink-0 flex items-center justify-center">
                                    @if ($item->product->images->first())
                                        <img src="{{ Storage::url($item->product->images->first()->image_path) }}"
                                            alt="{{ $item->product->name }}" width="56" height="56" loading="lazy"
                                            class="w-full h-full object-cover object-center">
                                    @else
                                        <i class="bi bi-image text-gray-300"></i>
                                    @endif
                                </div>
                                <div class="flex flex-col flex-1 gap-0.5">
                                    <span class="text-sm font-semibold">{{ $item->product->name }}</span>
                                    @if ($item->variant)
                                        <span class="text-xs text-gray-500">{{ __('order.size') }}:
                                            {{ $item->variant->label }}</span>
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
                            <span class="text-gray-500">{{ __('order.subtotal') }}</span>
                            <span
                                class="font-semibold">Rp{{ number_format($cartItems->sum(fn($i) => $i->product->price * $i->quantity), 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">{{ __('order.shipping') }}</span>
                            <span id="checkoutShippingDisplay"
                                class="text-gray-400">{{ __('checkout.not_selected') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-base font-bold pt-2 border-t border-black/10">
                            <span>{{ __('order.total') }}</span>
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
                    <h2 class="text-sm font-bold uppercase tracking-widest text-black/50">{{ __('checkout.recipient') }}
                    </h2>
                    <div>
                        <label for="customer_name"
                            class="block text-sm font-semibold mb-1.5">{{ __('checkout.full_name') }}</label>
                        <input type="text" id="customer_name" name="customer_name" autocomplete="name" required
                            maxlength="255" value="{{ old('customer_name', auth()->user()->name ?? '') }}"
                            class="w-full border border-black/10 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-black"
                            placeholder="{{ __('checkout.name_placeholder') }}">
                        @auth
                            <p class="text-xs text-gray-400 mt-1.5">{{ __('checkout.name_hint') }}</p>
                        @endauth
                    </div>
                    <div>
                        <label for="customer_phone"
                            class="block text-sm font-semibold mb-1.5">{{ __('checkout.phone') }}</label>
                        <input type="tel" id="customer_phone" name="customer_phone" inputmode="numeric"
                            pattern="[0-9]{9,15}" minlength="9" maxlength="15" autocomplete="tel" data-digits-only required
                            value="{{ old('customer_phone', preg_replace('/\D/', '', auth()->user()->phone ?? '')) }}"
                            class="w-full border border-black/10 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-black"
                            placeholder="08xxxxxxxxxx">
                        <p class="text-xs text-gray-400 mt-1.5">{{ __('checkout.phone_hint') }}</p>
                    </div>
                    <div>
                        <label for="customer_email"
                            class="block text-sm font-semibold mb-1.5">{{ __('checkout.email') }}</label>
                        <input type="email" id="customer_email" name="customer_email" autocomplete="email" required
                            maxlength="255" value="{{ old('customer_email', auth()->user()->email ?? '') }}"
                            class="w-full border border-black/10 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-black"
                            placeholder="nama@email.com">
                        <p class="text-xs text-gray-400 mt-1.5">{{ __('checkout.email_hint') }}</p>
                    </div>
                    <div class="relative">
                        <label for="destinationSearch"
                            class="block text-sm font-semibold mb-1.5">{{ __('checkout.destination') }}</label>
                        <input type="text" id="destinationSearch" autocomplete="off" role="combobox"
                            aria-autocomplete="list" aria-controls="destinationResults"
                            class="w-full border border-black/10 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-black"
                            placeholder="{{ __('checkout.destination_placeholder') }}">
                        <input type="hidden" name="destination_id" id="destinationId" value="{{ old('destination_id') }}">
                        <input type="hidden" name="destination_label" id="destinationLabel"
                            value="{{ old('destination_label') }}">
                        <div id="destinationResults"
                            class="hidden absolute top-full left-0 mt-1 w-full bg-white border border-black/10 rounded-lg shadow-lg max-h-60 overflow-y-auto z-20">
                        </div>
                        <p id="destinationSelected" class="text-xs text-gray-500 mt-1.5"></p>
                    </div>
                    <div>
                        <label for="customer_address"
                            class="block text-sm font-semibold mb-1.5">{{ __('checkout.address') }}</label>
                        <textarea id="customer_address" name="customer_address" rows="3" required maxlength="500"
                            autocomplete="street-address"
                            class="w-full border border-black/10 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-black"
                            placeholder="{{ __('checkout.address_placeholder') }}">{{ old('customer_address', auth()->user()->address ?? '') }}</textarea>
                    </div>
                </div>

                {{-- Pilihan kurir --}}
                <div id="shippingOptionsWrapper" class="hidden flex-col gap-3 border border-black/10 rounded-xl p-6">
                    <h2 class="text-sm font-bold uppercase tracking-widest text-black/50">{{ __('checkout.courier') }}
                    </h2>
                    <div id="shippingOptionsList" class="flex flex-col gap-2"></div>
                </div>
                <input type="hidden" name="shipping_courier" id="shippingCourier">
                <input type="hidden" name="shipping_service" id="shippingService">
                {{-- Cuma buat tampilan total di browser, gak ikut dikirim. Ongkir asli dihitung server. --}}
                <input type="hidden" id="shippingCost" value="0">

                {{-- Placeholder payment — nanti diisi Midtrans --}}
                <div class="flex flex-col gap-2 border border-black/10 rounded-xl p-6 bg-gray-50">
                    <h2 class="text-sm font-bold uppercase tracking-widest text-black/50">{{ __('checkout.payment') }}
                    </h2>
                    <p class="text-sm text-gray-500">{{ __('checkout.payment_note') }}</p>
                </div>

                <button type="submit" id="checkoutSubmitBtn"
                    class="bg-black hover:bg-black/80 disabled:opacity-60 text-white uppercase font-bold tracking-widest py-3.5 rounded-lg transition">
                    {{ __('checkout.place_order') }}
                </button>
            </form>

        </div>
    </section>

    <script>
        // Teks untuk JavaScript (ikut bahasa yang dipilih)
        const checkoutI18n = @js([
    'errorGeneric' => __('checkout.error_generic'),
    'notFound' => __('checkout.not_found'),
    'selected' => __('checkout.selected', ['label' => ':label']),
    'calculating' => __('checkout.calculating'),
    'calcFailed' => __('checkout.calc_failed'),
    'noService' => __('checkout.no_service'),
    'notSelected' => __('checkout.not_selected'),
    'destinationMissingTitle' => __('checkout.destination_missing_title'),
    'destinationMissingText' => __('checkout.destination_missing_text'),
    'courierMissingTitle' => __('checkout.courier_missing_title'),
    'courierMissingText' => __('checkout.courier_missing_text'),
    'processing' => __('checkout.processing'),
    'placeOrder' => __('checkout.place_order'),
]);

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
                fetch(`{{ route('shipping.search') }}?keyword=${encodeURIComponent(keyword)}`, {
                        headers: {
                            'Accept': 'application/json',
                        },
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (destinationSearch.value.trim() !== keyword)
                            return; // sudah mengetik hal lain
                        if (data.error) {
                            destinationResults.innerHTML =
                                `<p class="text-sm text-red-500 p-3">${escapeHtml(data.error)}</p>`;
                            destinationResults.classList.remove('hidden');
                            return;
                        }
                        renderDestinationResults(data.data);
                    })
                    .catch(() => {
                        destinationResults.innerHTML =
                            `<p class="text-sm text-gray-400 p-3">${escapeHtml(checkoutI18n.errorGeneric)}</p>`;
                        destinationResults.classList.remove('hidden');
                    });
            }, 300);
        });

        function renderDestinationResults(results) {
            if (results.length === 0) {
                destinationResults.innerHTML =
                    `<p class="text-sm text-gray-400 p-3">${escapeHtml(checkoutI18n.notFound)}</p>`;
                destinationResults.classList.remove('hidden');
                return;
            }

            destinationResults.innerHTML = results.map(item => `
                <button type="button"
                    class="destination-option w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50 border-b border-black/5 last:border-0"
                    data-id="${escapeHtml(item.id)}" data-label="${escapeHtml(item.label)}">
                    ${escapeHtml(item.label)}
                </button>
            `).join('');

            destinationResults.classList.remove('hidden');

            destinationResults.querySelectorAll('.destination-option').forEach(btn => {
                btn.addEventListener('click', () => {
                    destinationId.value = btn.dataset.id;
                    destinationLabel.value = btn.dataset.label;
                    destinationSearch.value = btn.dataset.label;
                    destinationSelected.textContent = checkoutI18n.selected.replace(':label', btn.dataset
                        .label);
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

        const cartSubtotal = {{ $cartItems->sum(fn($i) => $i->product->price * $i->quantity) }};

        function fetchShippingCost(destinationIdValue) {
            shippingOptionsList.innerHTML = `<p class="text-sm text-gray-400">${escapeHtml(checkoutI18n.calculating)}</p>`;
            shippingOptionsWrapper.classList.remove('hidden');
            shippingOptionsWrapper.classList.add('flex');

            fetch('{{ route('shipping.cost') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        destination_id: destinationIdValue,
                    }),
                })
                .then(res => res.json())
                .then(data => {
                    if (data.error) {
                        shippingOptionsList.innerHTML =
                            `<p class="text-sm text-red-500">${escapeHtml(data.error)}</p>`;
                        return;
                    }
                    renderShippingOptions(data.data);
                })
                .catch(() => {
                    shippingOptionsList.innerHTML =
                        `<p class="text-sm text-red-500">${escapeHtml(checkoutI18n.calcFailed)}</p>`;
                });
        }

        function renderShippingOptions(options) {
            if (!options || options.length === 0) {
                shippingOptionsList.innerHTML =
                    `<p class="text-sm text-gray-400">${escapeHtml(checkoutI18n.noService)}</p>`;
                return;
            }

            shippingOptionsList.innerHTML = options.map((opt, index) => `
                <label class="flex items-center justify-between gap-3 border border-black/10 rounded-lg px-4 py-3 cursor-pointer has-[:checked]:border-black has-[:checked]:bg-gray-50 transition">
                    <div class="flex items-center gap-3">
                        <input type="radio" name="shipping_option" value="${index}"
                            class="shipping-radio accent-black"
                            data-courier="${escapeHtml(opt.name)}" data-service="${escapeHtml(opt.service)}" data-cost="${Number(opt.cost) || 0}">
                        <div class="flex flex-col">
                            <span class="text-sm font-semibold">${escapeHtml(opt.name)} - ${escapeHtml(opt.service)}</span>
                            <span class="text-xs text-gray-500">${escapeHtml(opt.description)}${opt.etd ? ' · ' + escapeHtml(opt.etd) : ''}</span>
                        </div>
                    </div>
                    <span class="text-sm font-bold">Rp${(Number(opt.cost) || 0).toLocaleString('id-ID')}</span>
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
                'id-ID') : checkoutI18n.notSelected;
            document.getElementById('checkoutTotalDisplay').textContent = 'Rp' + total.toLocaleString('id-ID');
        }

        // ---- Validasi sebelum submit ----
        const checkoutForm = document.querySelector('form[action="{{ route('order.store') }}"]');
        checkoutForm.addEventListener('submit', (e) => {
            if (!destinationId.value) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: checkoutI18n.destinationMissingTitle,
                    text: checkoutI18n.destinationMissingText,
                    confirmButtonColor: '#B77B1C',
                });
                return;
            }
            if (!shippingCourier.value) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: checkoutI18n.courierMissingTitle,
                    text: checkoutI18n.courierMissingText,
                    confirmButtonColor: '#B77B1C',
                });
                return;
            }

            // Cegah klik ganda "Buat Pesanan" selama request berjalan
            const submitBtn = document.getElementById('checkoutSubmitBtn');
            submitBtn.disabled = true;
            submitBtn.textContent = checkoutI18n.processing;
        });

        // Tombol Back (bfcache) memulihkan halaman dengan tombol masih terkunci — buka lagi
        window.addEventListener('pageshow', (e) => {
            if (!e.persisted) return;
            const submitBtn = document.getElementById('checkoutSubmitBtn');
            submitBtn.disabled = false;
            submitBtn.textContent = checkoutI18n.placeOrder;
        });
    </script>
@endsection
