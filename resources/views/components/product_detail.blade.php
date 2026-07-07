<section class="flex flex-col w-full max-w-7xl gap-24 p-6 lg:p-14 pt-22 md:pt-18 lg:pt-28">
    <div class="">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-10 lg:gap-16">
            <!-- Image gallery -->
            <div class="flex flex-col gap-4">
                <!-- Main image -->
                <div class="relative w-full aspect-square bg-gray-100 overflow-hidden rounded-md">
                    <img src="{{ asset('aset/merch/Yalla-Front.png') }}" alt="Yalla"
                        class="absolute inset-0 w-full h-full object-contain" />
                </div>

                <!-- Sub Image -->
                <div class="flex gap-4">
                    <button
                        class="relative w-20 h-20 bg-gray-100 rounded-md overflow-hidden border border-transparent hover:border-gray-400">
                        <img src="{{ asset('aset/merch/Yalla-Back.png') }}" alt="Yalla"
                            class="absolute inset-0 w-full h-full object-contain" />
                    </button>
                </div>
            </div>

            <!-- Product info -->
            <div class="flex flex-col gap-6">
                <!-- Title -->
                <h1 class="text-2xl md:text-3xl font-semibold tracking-tight">
                    Yalla - Habibi Ekslusif Merch - <span class="text-amber-400">Yalla</span>
                </h1>
                <!-- Price -->
                <div class="flex items-baseline gap-3">
                    <span class="text-xl md:text-2xl font-semibold">Rp250.000</span>
                    <span class="text-sm text-red-600 font-medium">Sale</span>
                </div>
                <!-- Description -->
                <p class="text-sm md:text-base text-gray-700 leading-relaxed">
                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Cumque suscipit sequi vitae?
                </p>

                <!-- Materials & sustainability -->
                <div class="text-xs text-gray-500 leading-relaxed">
                    <h1 class="mb-2">
                        Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequuntur aperiam repudiandae sit
                        non cumque itaque odio iste. Rem ipsam quisquam tempora quia.
                    </h1>
                    <h1>
                        Lorem ipsum dolor sit amet consectetur adipisicing elit. Beatae, sed numquam. Qui dolor id
                        laborum esse nesciunt adipisci, exercitationem veniam quam velit quia modi.
                    </h1>
                </div>

                <!-- Sizes -->
                <div class="flex flex-col gap-3">
                    <span class="text-sm font-medium">Select Size</span>
                    <div class="flex flex-wrap gap-3">
                        <button onclick="selectSize('S')"
                            class="size-btn px-4 py-2 border border-gray-300 bg-white text-gray-900 rounded-md text-sm hover:border-gray-500">
                            S
                        </button>
                        <button onclick="selectSize('M')"
                            class="size-btn px-4 py-2 border border-black bg-black text-white rounded-md text-sm">
                            M
                        </button>
                        <button onclick="selectSize('L')"
                            class="size-btn px-4 py-2 border border-gray-300 bg-white text-gray-900 rounded-md text-sm opacity-50 cursor-not-allowed disabled"
                            disabled>
                            L
                        </button>
                        <button onclick="selectSize('XL')"
                            class="size-btn px-4 py-2 border border-gray-300 bg-white text-gray-900 rounded-md text-sm hover:border-gray-500">
                            XL
                        </button>
                    </div>
                    <p id="size-message" class="text-xs text-red-600"></p>
                </div>

                <script>
                    let selectedSize = null;

                    function selectSize(size) {
                        // Disable button has its own disabled state
                        if (size === 'L') {
                            document.getElementById('size-message').textContent = 'This size is not available.';
                            selectedSize = null;
                            updateButtons();
                            return;
                        }

                        selectedSize = size;
                        document.getElementById('size-message').textContent = '';
                        updateButtons();
                    }

                    function updateButtons() {
                        const buttons = document.querySelectorAll('.size-btn');
                        buttons.forEach(btn => {
                            const label = btn.textContent.trim();
                            if (label === selectedSize) {
                                // Active
                                btn.classList.remove('border-gray-300', 'bg-white', 'text-gray-900');
                                btn.classList.add('border-black', 'bg-black', 'text-white');
                            } else {
                                // Not active
                                if (label !== 'L') {
                                    btn.classList.remove('border-black', 'bg-black', 'text-white');
                                    btn.classList.add('border-gray-300', 'bg-white', 'text-gray-900');
                                }
                            }
                        });
                    }
                </script>
                <!-- Sizes End -->

                <!-- Quantity + Add to cart -->
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex items-center border rounded-md">
                        <button id="qty-minus" type="button" class="px-3 py-2 text-gray-700 hover:text-black">
                            -
                        </button>
                        <input id="qty-input" type="number" min="1" value="1"
                            class="w-12 text-center outline-none" />
                        <button id="qty-plus" type="button" class="px-3 py-2 text-gray-700 hover:text-black">
                            +
                        </button>
                    </div>

                    <script>
                        const qtyInput = document.getElementById('qty-input');
                        const qtyMinus = document.getElementById('qty-minus');
                        const qtyPlus = document.getElementById('qty-plus');

                        function updateButtons() {
                            const value = parseInt(qtyInput.value, 10) || 1;
                            qtyMinus.disabled = value <= 1;
                            qtyMinus.classList.toggle('opacity-50', value <= 1);
                            qtyMinus.classList.toggle('cursor-not-allowed', value <= 1);
                        }

                        qtyMinus.addEventListener('click', () => {
                            const value = parseInt(qtyInput.value, 10) || 1;
                            if (value > 1) {
                                qtyInput.value = value - 1;
                            }
                            updateButtons();
                        });

                        qtyPlus.addEventListener('click', () => {
                            const value = parseInt(qtyInput.value, 10) || 1;
                            qtyInput.value = value + 1;
                            updateButtons();
                        });

                        qtyInput.addEventListener('input', () => {
                            const value = parseInt(qtyInput.value, 10);
                            if (value === null || value < 1) {
                                qtyInput.value = 1;
                            }
                            updateButtons();
                        });

                        // Initial state
                        updateButtons();
                    </script>
                    
                    <button class="px-6 py-2 bg-black text-white text-sm font-medium rounded-md hover:bg-gray-800">
                        Add to Cart
                    </button>
                </div>

            </div>
        </div>
    </div>
</section>
