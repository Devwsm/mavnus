# Mavnus

E-commerce untuk merchandise resmi Whisnu Santika (clothing & accessories). Ada storefront buat customer dan dashboard internal buat tim (owner, admin produk, staff pesanan).

## Fitur

**Storefront (customer)**

- Katalog 2 kategori: Clothes (varian ukuran S/M/L/XL) dan Accessories (keychain, sticker, totebag)
- Filter produk berdasarkan rentang harga, plus live search suggestion (`/search`, rate-limited)
- Keranjang belanja berbasis session
- Checkout dengan hitung ongkos kirim otomatis lewat RajaOngkir (cari tujuan + hitung biaya). Ongkir dihitung ulang di server saat pesanan dibuat (angka dari browser tidak dipakai), dan stok dikunci selama transaksi biar tidak oversell. Nomor HP wajib angka saja, 9-15 digit
- Halaman sukses pesanan hanya bisa dibuka browser yang membuat pesanan itu (tercatat di session) atau pemilik akunnya
- Akun customer (opsional): riwayat & status pesanan, edit profil, hapus akun. Pesanan lama tetap tersimpan buat rekap staff, cuma dilepas dari akunnya (`user_id` jadi `null`, jadi tercatat seperti pesanan guest)
- Jadwal rilis produk (`published_at`): otomatis muncul begitu waktunya tiba
- Status stok sinkron otomatis (produk jadi "habis" kalau stok/varian habis)
- Pesanan `pending` yang gak dibayar dalam waktu tertentu otomatis dibatalkan & dihapus (jalan lewat middleware, gak butuh cron)
- Sitemap XML otomatis

**Dashboard staff**

- Landing beda tiap role. Landing owner nampilin omzet 6 bulan terakhir, omzet per kategori bulan berjalan, dan produk terlaris (dihitung dari pesanan `completed`)
- CRUD produk: upload multi-foto otomatis dikonversi ke WebP, atur varian ukuran & stok per kategori
- Kelola status pesanan: pending → processing → shipped → completed
- Statistik pengunjung (device, browser, halaman terbanyak), dicatat lewat middleware tanpa analytics eksternal
- Export: pesanan (Excel), invoice pesanan (PDF), data produk (SQL), backup database & storage (khusus owner)
- Login staff terpisah dari customer (tabel `accounts`, bukan `users`) dengan rate limiting

## Route

### Customer

| Halaman                     | Route                                        | Login |
| --------------------------- | -------------------------------------------- | ----- |
| Beranda                     | `/`                                          | -     |
| Daftar & detail Clothes     | `/clothes`, `/clothes/{slug}`                | -     |
| Daftar & detail Accessories | `/accessoris`, `/accessoris/{slug}`          | -     |
| Keranjang                   | `/cart`                                      | -     |
| Checkout & sukses pesanan   | `/order/checkout`, `/order/{order}/success`  | -     |
| Info footer                 | `/info`                                      | -     |
| Login / daftar              | `/login`, `/register`                        | -     |
| Akun, edit profil           | `/account`, `/account/edit`                  | ya    |
| Riwayat & detail pesanan    | `/account/orders`, `/account/orders/{order}` | ya    |
| Sitemap                     | `/sitemap.xml`                               | -     |

### Staff (login lewat `/crew-portal`)

| Halaman              | Route                                              | Role                               |
| -------------------- | -------------------------------------------------- | ---------------------------------- |
| Dashboard            | `/dashboard`                                       | semua role staff                   |
| Pesanan              | `/dashboard/orders`, `/dashboard/orders/{order}`   | owner, staff_pesanan               |
| Produk               | `/dashboard/produk`                                | owner, admin_produk                |
| Statistik pengunjung | `/dashboard/visitors`, `/dashboard/visitors/pages` | owner                              |
| Import/Export        | `/dashboard/import-export`                         | semua role (tombol beda tiap role) |

Role dicek lewat middleware `role:...` di `routes/web.php`, berdasarkan data session yang diisi saat login.

## Tech Stack

**Backend**

- Laravel 13 (PHP 8.3+), MySQL
- Auth bawaan Laravel (tabel `users`) buat customer; session, cache, dan queue pakai driver `database`
- `barryvdh/laravel-dompdf` (invoice PDF), `maatwebsite/excel` (export Excel), `intervention/image` + `intervention/image-laravel` (konversi gambar ke WebP)

**Frontend**

- Blade (server-rendered) + vanilla JS, tanpa framework JS
- Tailwind CSS v4 (`@tailwindcss/vite`) dan Vite
- `bootstrap-icons` (icon) dan `sweetalert2` (dialog konfirmasi)

**Layanan eksternal**

- RajaOngkir (Komerce) lewat `App\Services\RajaOngkirService`: cari tujuan & hitung ongkir

## Belum Diimplementasikan

- **Pembayaran Midtrans.** Kolom `midtrans_order_id` & `midtrans_transaction_id` sudah ada di tabel `orders`, tapi integrasinya belum dibuat. Checkout saat ini masih placeholder/manual.
- **Login Google.** Belum ada. Route dan tombolnya sudah dihapus. `accountController` masih mengecek `$user->google_id` (selalu `null` karena kolomnya belum ada di tabel `users`). Kalau fitur ini dilanjutkan: install `laravel/socialite`, tambah kolom `google_id`, buat route + method di `authController`.

Env var tambahan yang dipakai kode tapi belum ada di `.env.example`:

| Variabel                         | Keterangan                                                           |
| -------------------------------- | -------------------------------------------------------------------- |
| `RAJAONGKIR_API_KEY`             | wajib, buat hitung ongkir                                            |
| `RAJAONGKIR_BASE_URL`            | wajib, mis. `https://rajaongkir.komerce.id/api/v1`                   |
| `RAJAONGKIR_ORIGIN_ID`           | wajib, ID lokasi asal pengiriman                                     |
| `ORDER_EXPIRE_MINUTES`           | opsional, default 60. Batas waktu pesanan pending sebelum dibatalkan |
| `ORDER_CLEANUP_THROTTLE_MINUTES` | opsional, default 5. Jarak minimal antar-proses cleanup              |

Seeder bikin 3 akun staff contoh: `owner@mavnus.com` (Owner), `admin@mavnus.com` (Admin Produk), `staff@mavnus.com` (Staff Pesanan). Password awalnya ada di `database/seeders/accountSeeder.php`, **wajib diganti** sebelum dipakai di production. Login lewat `/crew-portal` (terpisah dari `/login` buat customer).

Untuk production: set `APP_ENV=production` dan `APP_DEBUG=false`, lalu jalankan `npm run build`.

## Struktur Folder

```
app/
├── Exceptions/       # RajaOngkirException
├── Exports/          # Export pesanan ke Excel
├── Http/
│   ├── Controllers/  # Satu controller per area: account, auth, cart, dashboard, home,
│   │                 #   importExport, login (staff), order, product, search, shipping,
│   │                 #   sitemap, visitor
│   └── Middleware/   # cekLogin & cekRole (guard staff), AutoCancelExpiredOrders, TrackVisit
├── Models/           # product (+ clothes / accessoris / ProductVariant / ProductImage),
│                     #   Order & OrderItem, CartItem, User (customer), account (staff), Visit
├── Services/         # RajaOngkirService
└── Support/          # OrderCleanup (pembatalan pesanan pending yang expired)

database/             # migrations, seeders, factories
resources/
├── css/app.css       # entry Tailwind
├── js/app.js         # SweetAlert2 + helper input angka (data-digits-only)
└── views/
    ├── components/   # card produk, navbar, footer, cart, filter, field form dashboard, dll
    ├── pages/        # storefront, akun, checkout, dan pages/dashboard/* buat staff
    ├── template/     # layout dasar (layout, bare-layout, account-layout, dashboard/layout)
    ├── errors/       # 404, 429, 500, 503
    └── exports/      # template invoice PDF
routes/web.php        # semua route web
scripts/              # build-icons.cjs (font ikon subset) & optimize-images.cjs (WebP/favicon) — jalan otomatis sebelum npm run dev/build
public/aset/          # gambar statis (logo, banner, halaman maintenance)
```

## Testing

Test ada di `tests/Feature` dan memakai SQLite in-memory (lihat `phpunit.xml`), jadi tidak menyentuh database MySQL kamu.

```bash
php artisan test
# atau satu file saja:
php artisan test tests/Feature/CheckoutTest.php
```

Test foto produk (resize ke WebP) otomatis di-skip kalau ekstensi PHP GD tidak terpasang.

## Aset hasil generate (ikon & gambar)

Font ikon (`resources/fonts/bootstrap-icons-subset.woff2`) dan gambar WebP/favicon di `public/aset` dibuat oleh script, bukan ditulis tangan:

```bash
npm install        # sekali saja (butuh paket subset-font & sharp)
npm run icons      # font ikon subset + resources/css/icons.css
npm run images     # logo/banner/maintenance .webp + favicon-192.png
```

Keduanya jalan otomatis sebelum `npm run dev` dan `npm run build`. Kalau menambah ikon `bi-*` baru di view, cukup jalankan `npm run dev`/`build` lagi. Kalau file `.webp` belum ada, view otomatis memakai gambar asli (`App\Support\Img`).

