<button type="button" onclick="openCart()" aria-label="Buka keranjang belanja"
    class="relative inline-flex min-h-11 min-w-11 items-center justify-center text-lg">
    <i class="bi bi-bag" aria-hidden="true"></i>
    {{-- Jumlah dirender server ($cartCount dari View composer), jadi tidak perlu fetch() tambahan tiap halaman dibuka --}}
    <span
        class="cart-badge {{ ($cartCount ?? 0) > 0 ? 'flex' : 'hidden' }} absolute top-1 right-1 bg-[#B71C1C] text-white text-[10px] font-bold w-4 h-4 rounded-full items-center justify-center">{{ ($cartCount ?? 0) > 0 ? $cartCount : '' }}</span>
</button>

{{-- Cart Backdrop --}}
<div id="cartBackdrop"
    class="fixed inset-0 bg-black/50 z-70 opacity-0 pointer-events-none transition-opacity duration-300">
</div>

{{-- Cart Drawer (half-screen, dari kanan) --}}
<div id="cartDrawer" role="dialog" aria-modal="true" aria-label="Keranjang belanja" aria-hidden="true"
    class="fixed top-0 right-0 h-full w-3/4 md:w-1/2 bg-black text-white z-80 invisible
    flex flex-col translate-x-full transition-transform duration-300">

    <div class="flex items-center justify-between p-6 border-b border-white/10">
        <h2 class="text-xl font-bold uppercase tracking-wide">Cart</h2>
        <button id="cartCloseBtn" type="button" aria-label="Tutup keranjang belanja"
            class="inline-flex min-h-11 min-w-11 items-center justify-center text-3xl">
            <i class="bi bi-x" aria-hidden="true"></i>
        </button>
    </div>

    <div id="cartItemsWrapper" class="flex-1 overflow-y-auto p-6 flex flex-col gap-4">
        <p class="text-white/60 text-sm text-center py-10">Keranjang masih kosong.</p>
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
            cartDrawer.classList.remove('translate-x-full', 'invisible');
            cartDrawer.setAttribute('aria-hidden', 'false');
            cartBackdrop.classList.remove('opacity-0', 'pointer-events-none');
            document.body.classList.add('overflow-hidden');
        }

        function closeCart() {
            cartDrawer.classList.add('translate-x-full');
            // invisible ditunda sampai animasi geser selesai, supaya elemen di drawer yang
            // tersembunyi tidak bisa difokus lewat keyboard (Tab)
            setTimeout(() => {
                if (cartDrawer.classList.contains('translate-x-full')) cartDrawer.classList.add('invisible');
            }, 300);
            cartDrawer.setAttribute('aria-hidden', 'true');
            cartBackdrop.classList.add('opacity-0', 'pointer-events-none');
            document.body.classList.remove('overflow-hidden');
        }

        function fetchCart() {
            fetch('{{ route('cart.index') }}', {
                    headers: {
                        'Accept': 'application/json',
                    },
                })
                .then(res => {
                    if (!res.ok) throw new Error('HTTP ' + res.status);
                    return res.json();
                })
                .then(renderCart)
                .catch(() => {
                    cartItemsWrapper.innerHTML =
                        '<p class="text-white/60 text-sm text-center py-10">Gagal memuat keranjang. Coba lagi.</p>';
                });
        }

        function renderCart(data) {
            updateCartBadge(data.count);
            cartTotal.textContent = 'Rp' + data.total.toLocaleString('id-ID');

            if (data.items.length === 0) {
                cartItemsWrapper.innerHTML =
                    '<p class="text-white/60 text-sm text-center py-10">Keranjang masih kosong.</p>';
                return;
            }

            cartItemsWrapper.innerHTML = data.items.map(item => `
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="w-20 h-20 rounded-lg overflow-hidden bg-[#0D0D0D] shrink-0 flex items-center justify-center">
                        ${item.image ? `<img src="${escapeHtml(item.image)}" alt="${escapeHtml(item.name)}" width="80" height="80" loading="lazy" class="w-full h-full object-cover object-center">` : `<i class="bi bi-image text-white/20 text-xl" aria-hidden="true"></i>`}
                    </div>
                    <div class="flex flex-col flex-1 gap-1.5">
                        <span class="text-base font-semibold">${escapeHtml(item.name)}</span>
                        ${item.size ? `<span class="text-sm text-white/60">Size: ${escapeHtml(item.size)}</span>` : ''}
                        <div class="flex items-center justify-between mt-1">
                            <div class="flex items-center gap-3 border border-white/10 rounded-lg">
                                <button type="button" onclick="changeQty(${Number(item.id)}, ${Number(item.quantity) - 1})" aria-label="Kurangi jumlah ${escapeHtml(item.name)}" class="min-h-11 min-w-11 px-3 py-1.5 text-white/60 hover:text-white text-lg disabled:opacity-30">-</button>
                                <span class="text-base" aria-live="polite">${Number(item.quantity)}</span>
                                <button type="button" onclick="changeQty(${Number(item.id)}, ${Number(item.quantity) + 1})" ${item.quantity >= item.max ? 'disabled' : ''} aria-label="Tambah jumlah ${escapeHtml(item.name)}" class="min-h-11 min-w-11 px-3 py-1.5 text-white/60 hover:text-white text-lg disabled:opacity-30">+</button>
                            </div>
                            <button type="button" onclick="removeCartItem(${Number(item.id)})" aria-label="Hapus ${escapeHtml(item.name)} dari keranjang" class="min-h-11 min-w-11 text-white/60 hover:text-[#B71C1C] text-base disabled:opacity-30">
                                <i class="bi bi-trash" aria-hidden="true"></i>
                            </button>
                        </div>
                        <span class="text-sm text-white/60">${escapeHtml(item.subtotal)}</span>
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
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        quantity
                    }),
                })
                .then(res => res.json())
                .then(renderCart)
                .catch(() => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal mengubah jumlah',
                        text: 'Terjadi kesalahan, coba lagi.',
                        confirmButtonColor: '#B71C1C',
                    });
                })
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
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                })
                .then(res => res.json())
                .then(renderCart)
                .catch(() => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal menghapus item',
                        text: 'Terjadi kesalahan, coba lagi.',
                        confirmButtonColor: '#B71C1C',
                    });
                })
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

        // Badge sudah dirender server. Saat tombol Back memulihkan halaman dari cache browser (bfcache),
        // angkanya bisa basi — baru di kasus itu kita fetch ulang.
        window.addEventListener('pageshow', (e) => {
            if (e.persisted) fetchCart();
        });
    </script>
@endonce
