{{--
    Menu navigasi akun, dipakai bareng di pages.account & pages.account-orders.
    Props: $active ('account' | 'orders')
    Gaya "app shell": kartu profil gradasi + shadow lembut (bukan border tebal),
    list menu rounded dengan ikon berwarna, mirip Shopee/Tokopedia.
--}}
<div class="flex flex-col gap-3">
    {{-- Kartu profil (membership-card style) --}}
    <div
        class="relative overflow-hidden rounded-2xl bg-linear-to-br from-neutral-900 via-black to-neutral-800 text-white p-5 shadow-lg shadow-black/10">
        {{-- Aksen dekoratif --}}
        <div class="pointer-events-none absolute -right-8 -top-10 w-36 h-36 rounded-full bg-white/5"></div>
        <div class="pointer-events-none absolute -right-2 bottom-0 w-20 h-20 rounded-full bg-white/5"></div>

        <div class="relative flex items-center gap-4">
            <div class="relative shrink-0">
                <div
                    class="w-16 h-16 rounded-full bg-white/10 ring-2 ring-white/25 flex items-center justify-center text-2xl font-black uppercase">
                    {{ Str::substr($user->name, 0, 1) }}
                </div>
                <a href="{{ route('account.edit') }}" aria-label="Edit Profil"
                    class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full bg-white text-black flex items-center justify-center shadow ring-2 ring-black active:scale-90 transition">
                    <i class="bi bi-pencil-fill text-[10px]"></i>
                </a>
            </div>
            <div class="min-w-0 flex-1">
                <p class="font-semibold truncate leading-tight">{{ $user->name }}</p>
                <p class="text-xs text-white/60 truncate mt-0.5">{{ $user->email }}</p>
            </div>
        </div>
    </div>

    {{-- Mobile: navigasi menu & logout sudah ada di bottom nav (components.account.bottom-nav),
        jadi di sini cukup kartu profil di atas, tanpa list menu lagi biar gak dobel. --}}

    {{-- Desktop: sidebar vertikal, shadow lembut --}}
    <nav class="hidden lg:flex flex-col bg-white rounded-2xl overflow-hidden shadow-sm shadow-black/5">
        <a href="{{ route('account') }}"
            class="flex items-center gap-3 px-5 py-3.5 text-sm font-semibold tracking-wide transition {{ $active === 'account' ? 'bg-black text-white' : 'text-gray-600 hover:bg-gray-50' }}">
            <i class="bi bi-person-fill"></i> Profil Saya
        </a>
        <a href="{{ route('account.orders') }}"
            class="flex items-center gap-3 px-5 py-3.5 text-sm font-semibold tracking-wide transition {{ $active === 'orders' ? 'bg-black text-white' : 'text-gray-600 hover:bg-gray-50' }}">
            <i class="bi bi-bag-fill"></i> Riwayat Pesanan
        </a>
        <a href="{{ route('logout') }}"
            class="flex items-center gap-3 px-5 py-3.5 text-sm font-semibold tracking-wide hover:bg-gray-50 transition text-gray-400">
            <i class="bi bi-box-arrow-right"></i> Keluar
        </a>
    </nav>
</div>
