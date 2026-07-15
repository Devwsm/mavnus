<button type="button" onclick="openCart()" class="relative inline-flex text-lg">
    <i class="bi bi-bag"></i>
    <span
        class="cart-badge hidden absolute -top-2 -right-2 bg-[#B71C1C] text-white text-[10px] font-bold w-4 h-4 rounded-full items-center justify-center"></span>
</button>

{{-- Cart Backdrop --}}
<div id="cartBackdrop"
    class="fixed inset-0 bg-black/50 z-70 opacity-0 pointer-events-none transition-opacity duration-300">
</div>

{{-- Cart Drawer (half-screen, dari kanan) --}}
<div id="cartDrawer"
    class="fixed top-0 right-0 h-full w-3/4 md:w-1/2 bg-black text-white z-80
    flex flex-col translate-x-full transition-transform duration-300">

    <div class="flex items-center justify-between p-6 border-b border-white/10">
        <h2 class="text-xl font-bold uppercase tracking-wide">Cart</h2>
        <button id="cartCloseBtn" class="text-3xl">
            <i class="bi bi-x"></i>
        </button>
    </div>

    <div id="cartItemsWrapper" class="flex-1 overflow-y-auto p-6 flex flex-col gap-4">
        <p class="text-white/40 text-sm text-center py-10">Keranjang masih kosong.</p>
    </div>

    <div class="border-t border-white/10 p-6 flex flex-col gap-4">
        <div class="flex items-center justify-between">
            <span class="text-white/60 text-sm">Total</span>
            <span id="cartTotal" class="text-lg font-bold">Rp0</span>
        </div>
        <a href="{{ route('order.checkout') }}"
            class="bg-[#B71C1C] hover:bg-[#891212] text-white uppercase font-bold tracking-widest py-3 rounded-lg transition text-center">
            Checkout
        </a>
    </div>
</div>

@once
    <script>
        const cartBackdrop = document.getElementById('cartBackdrop');
        const cartDrawer = document.getElementById('cartDrawer');
        const cartCloseBtn = document.getElementById('cartCloseBtn');
        const cartItemsWrapper = document.getElementById('cartItemsWrapper');
        const cartTotal = document.getElementById('cartTotal');

        let cartRequestInProgress = false;

        function openCart() {
            fetchCart();
            cartDrawer.classList.remove('translate-x-full');
            cartBackdrop.classList.remove('opacity-0', 'pointer-events-none');
            document.body.classList.add('overflow-hidden');
        }

        function closeCart() {
            cartDrawer.classList.add('translate-x-full');
            cartBackdrop.classList.add('opacity-0', 'pointer-events-none');
            document.body.classList.remove('overflow-hidden');
        }

        function fetchCart() {
            fetch('{{ route('cart.index') }}')
                .then(res => res.json())
                .then(renderCart);
        }

        function renderCart(data) {
            updateCartBadge(data.count);
            cartTotal.textContent = 'Rp' + data.total.toLocaleString('id-ID');

            if (data.items.length === 0) {
                cartItemsWrapper.innerHTML =
                    '<p class="text-white/40 text-sm text-center py-10">Keranjang masih kosong.</p>';
                return;
            }

            cartItemsWrapper.innerHTML = data.items.map(item => `
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="w-20 h-20 rounded-lg overflow-hidden bg-[#0D0D0D] shrink-0 flex items-center justify-center">
                        ${item.image ? `<img src="${item.image}" class="w-full h-full object-cover object-center">` : `<i class="bi bi-image text-white/20 text-xl"></i>`}
                    </div>
                    <div class="flex flex-col flex-1 gap-1.5">
                        <span class="text-base font-semibold">${item.name}</span>
                        ${item.size ? `<span class="text-sm text-white/40">Size: ${item.size}</span>` : ''}
                        <div class="flex items-center justify-between mt-1">
                            <div class="flex items-center gap-3 border border-white/10 rounded-lg">
                                <button onclick="changeQty(${item.id}, ${item.quantity - 1})" class="px-3 py-1.5 text-white/60 hover:text-white text-lg disabled:opacity-30">-</button>
                                <span class="text-base">${item.quantity}</span>
                                <button onclick="changeQty(${item.id}, ${item.quantity + 1})" ${item.quantity >= item.max ? 'disabled' : ''} class="px-3 py-1.5 text-white/60 hover:text-white text-lg disabled:opacity-30">+</button>
                            </div>
                            <button onclick="removeCartItem(${item.id})" class="text-white/40 hover:text-[#B71C1C] text-base disabled:opacity-30">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                        <span class="text-sm text-white/50">${item.subtotal}</span>
                    </div>
                </div>
            `).join('');
        }

        function changeQty(id, quantity) {
            if (cartRequestInProgress) return;

            if (quantity < 1) {
                removeCartItem(id);
                return;
            }

            cartRequestInProgress = true;
            setCartButtonsDisabled(true);

            fetch(`/cart/${id}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        quantity
                    }),
                })
                .then(res => res.json())
                .then(renderCart)
                .finally(() => {
                    cartRequestInProgress = false;
                });
        }

        function removeCartItem(id) {
            if (cartRequestInProgress) return;

            cartRequestInProgress = true;
            setCartButtonsDisabled(true);

            fetch(`/cart/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                })
                .then(res => res.json())
                .then(renderCart)
                .finally(() => {
                    cartRequestInProgress = false;
                });
        }

        function setCartButtonsDisabled(disabled) {
            cartItemsWrapper.querySelectorAll('button').forEach(btn => {
                btn.disabled = disabled;
                btn.classList.toggle('opacity-40', disabled);
            });
        }

        function updateCartBadge(count) {
            document.querySelectorAll('.cart-badge').forEach(badge => {
                badge.textContent = count;
                badge.classList.toggle('hidden', count === 0);
                badge.classList.toggle('flex', count > 0);
            });
        }

        cartCloseBtn.addEventListener('click', closeCart);
        cartBackdrop.addEventListener('click', closeCart);

        // Muat jumlah item cart begitu halaman dibuka (untuk badge di navbar)
        fetchCart();
    </script>
@endonce
