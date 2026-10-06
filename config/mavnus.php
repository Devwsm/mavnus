<?php

// Semua nilai env() yang dipakai aplikasi ditaruh di sini, BUKAN dibaca langsung
// pakai env() di kode. Setelah `php artisan config:cache` (wajib di production),
// env() di luar folder config/ selalu return null — jadi nilai di .env diam-diam
// diabaikan dan jatuh ke default.
return [
    // Berapa menit pesanan "pending & belum dibayar" dibiarkan sebelum otomatis dibatalkan
    'order_expire_minutes' => (int) env('ORDER_EXPIRE_MINUTES', 60),

    // Jeda minimal (menit) antar pengecekan pesanan kedaluwarsa
    'order_cleanup_throttle_minutes' => (int) env('ORDER_CLEANUP_THROTTLE_MINUTES', 5),

    // Isi keranjang yang tidak disentuh selama ini (hari) akan dibersihkan
    'cart_retention_days' => (int) env('CART_RETENTION_DAYS', 30),
];