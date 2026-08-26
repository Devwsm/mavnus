{{--
    Bottom nav fixed khusus halaman akun, gaya app (mirip Shopee/Tokopedia).
    Cuma tampil di mobile/tablet (lg:hidden) — desktop udah ada sidebar
    dari components.account.menu. Sengaja gak ada tombol "Keluar" di sini
    biar gak ke-tap gak sengaja; logout tetap di dalam menu halaman akun.

    Props:
    - $active ('home' | 'orders' | 'account') — dari halaman yang manggil
--}}
@php
    $navItems = [
        [
            'key' => 'home',
            'label' => 'Beranda',
            'icon' => 'bi-house',
            'iconActive' => 'bi-house-fill',
            'route' => route('home'),
        ],
        [
            'key' => 'orders',
            'label' => 'Pesanan',
            'icon' => 'bi-bag',
            'iconActive' => 'bi-bag-fill',
            'route' => route('account.orders'),
        ],
        [
            'key' => 'account',
            'label' => 'Akun',
            'icon' => 'bi-person',
            'iconActive' => 'bi-person-fill',
            'route' => route('account'),
        ],
    ];
@endphp
<nav aria-label="Navigasi akun"
    class="lg:hidden fixed bottom-0 inset-x-0 z-40 bg-white border-t border-gray-100 shadow-[0_-4px_16px_rgba(0,0,0,0.06)]"
    style="padding-bottom: env(safe-area-inset-bottom, 0px);">
    <div class="grid grid-cols-3">
        @foreach ($navItems as $item)
            @php $isActive = $active === $item['key']; @endphp
            <a href="{{ $item['route'] }}" aria-current="{{ $isActive ? 'page' : 'false' }}"
                class="flex flex-col items-center justify-center gap-1 py-2.5 active:bg-gray-50 transition">
                <i
                    class="bi {{ $isActive ? $item['iconActive'] : $item['icon'] }} text-lg {{ $isActive ? 'text-black' : 'text-gray-400' }}"></i>
                <span
                    class="text-[11px] font-semibold {{ $isActive ? 'text-black' : 'text-gray-400' }}">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </div>
</nav>
