{{--
    Header sapaan + badge role, dipakai di semua landing dashboard
    (owner, admin produk, staff pesanan). Sengaja gak nerima props,
    langsung baca dari session biar simpel dipanggil di mana aja
    (session role/name diisi loginController pas login).
--}}
@php
    $roleMeta = [
        'owner' => [
            'label' => 'Owner',
            'badge' => 'bg-amber-400/10 text-amber-400 border-amber-400/25',
        ],
        'admin_produk' => [
            'label' => 'Admin Produk',
            'badge' => 'bg-blue-400/10 text-blue-400 border-blue-400/25',
        ],
        'staff_pesanan' => [
            'label' => 'Staff Pesanan',
            'badge' => 'bg-green-400/10 text-green-400 border-green-400/25',
        ],
    ];
    $meta = $roleMeta[session('role')] ?? ['label' => 'Staff', 'badge' => 'bg-white/10 text-white/60 border-white/20'];
    $displayName = session('name') ?: session('user');
@endphp
<div class="flex flex-col items-center gap-2.5 p-8 text-center">
    <span
        class="inline-flex items-center gap-1.5 border {{ $meta['badge'] }} text-[11px] font-bold uppercase tracking-wide px-3 py-1 rounded-full">
        <i class="bi bi-shield-check"></i> {{ $meta['label'] }}
    </span>
    <h1 class="text-2xl md:text-3xl font-bold">Halo, {{ $displayName }}</h1>
</div>
