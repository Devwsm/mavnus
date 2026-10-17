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

    // Kirim email konfirmasi ke pembeli setelah checkout. Default MATI: aktifkan hanya setelah
    // MAIL_* di .env server (SMTP) sudah diisi dan diuji.
    'order_email_enabled' => (bool) env('ORDER_EMAIL_ENABLED', false),

    // Bahasa yang tersedia di website (kode => nama asli bahasanya). File terjemahan ada di folder lang/.
    // Dibaca langsung dari config (bukan APP_LOCALE di .env) supaya default selalu Bahasa Indonesia.
    'locales' => [
        'id' => 'Bahasa Indonesia',
        'en' => 'English',
    ],

    // Bahasa yang dipakai kalau pengunjung belum pernah memilih
    'default_locale' => 'id',
];