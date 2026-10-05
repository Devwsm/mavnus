# Mavnus

E-commerce untuk merchandise resmi Whisnu Santika (clothing & accessories). Ada storefront buat customer dan dashboard internal buat tim (owner, admin produk, staff pesanan).

## Fitur

**Storefront (customer)**

- Katalog 2 kategori: Clothes (varian ukuran S/M/L/XL) dan Accessories (keychain, sticker, totebag)
- Filter produk berdasarkan rentang harga, plus live search suggestion (`/search`, rate-limited)
- Keranjang belanja berbasis session
- Checkout dengan hitung ongkos kirim otomatis lewat RajaOngkir (cari tujuan + hitung biaya). Nomor HP wajib angka saja, 9-15 digit
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
- **Login Google.** Tombol "Masuk dengan Google" di halaman login cuma UI. Route `/login/google` dan `/login/google/callback` sudah terdaftar tapi method `redirectToGoogle` / `handleGoogleCallback` belum ada di `authController`, jadi kalau diakses langsung hasilnya error 500. Package `laravel/socialite` belum ter-install dan kolom `google_id` (dicek di `accountController`) belum ada di tabel `users`.


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
scripts/              # patch-bootstrap-icons.cjs (jalan otomatis saat npm install)
public/aset/          # gambar statis (logo, banner, halaman maintenance)
```
