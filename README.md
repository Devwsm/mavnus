# Mavnus

E-commerce untuk merchandise resmi Whisnu Santika (clothing & accessories). Ada storefront buat customer dan dashboard internal buat tim (owner, admin produk, staff pesanan).

## Halaman yang Bisa Diakses

### Customer (publik / tanpa login, kecuali disebutkan)

| Halaman                                                      | Route                     |
| ------------------------------------------------------------ | ------------------------- |
| Beranda                                                      | `/`                       |
| Daftar produk Clothes (dengan filter harga & pagination)     | `/clothes`                |
| Detail produk Clothes                                        | `/clothes/{slug}`         |
| Daftar produk Accessories (dengan filter harga & pagination) | `/accessoris`             |
| Keranjang belanja                                            | `/cart`                   |
| Checkout                                                     | `/order/checkout`         |
| Halaman sukses pesanan                                       | `/order/{order}/success`  |
| Info / halaman footer                                        | `/info`                   |
| Login / daftar akun customer                                 | `/login`, `/register`     |
| Akun saya _(login)_                                          | `/account`                |
| Edit profil _(login)_                                        | `/account/edit`           |
| Riwayat pesanan saya _(login)_                               | `/account/orders`         |
| Detail pesanan saya _(login)_                                | `/account/orders/{order}` |
| Sitemap XML                                                  | `/sitemap.xml`            |

Selain itu ada juga aksi `POST /account` (update profil), `DELETE /account` (hapus akun sendiri — order lama tetap disimpan tapi `user_id`-nya jadi null, jadi ke depannya tercatat seperti pesanan guest) dan `GET/POST /login/google` (lihat catatan di bawah, **belum jalan**).

### Staff / internal (login lewat `/crew-portal`, role-based)

| Halaman                               | Route                       | Role yang bisa akses                                           |
| ------------------------------------- | --------------------------- | -------------------------------------------------------------- |
| Login staff                           | `/crew-portal`              | —                                                              |
| Dashboard (landing beda tiap role)    | `/dashboard`                | owner, admin_produk, staff_pesanan                             |
| Kelola pesanan                        | `/dashboard/orders`         | owner, staff_pesanan                                           |
| Detail pesanan                        | `/dashboard/orders/{order}` | owner, staff_pesanan                                           |
| Kelola produk (clothes & accessories) | `/dashboard/produk`         | owner, admin_produk                                            |
| Statistik pengunjung                  | `/dashboard/visitors`       | owner                                                          |
| Statistik pengunjung — per halaman    | `/dashboard/visitors/pages` | owner                                                          |
| Import/Export data                    | `/dashboard/import-export`  | owner, admin_produk, staff_pesanan (isi tombol beda tiap role) |

3 role staff: **Owner** (akses penuh), **Admin Produk** (kelola produk & stok), **Staff Pesanan** (kelola pesanan). Role & hak akses diatur lewat middleware `role:...` di `routes/web.php`, dicek dari data session yang diisi pas login lewat `/crew-portal`.

## Fitur

**Storefront (customer)**

- Katalog produk 2 kategori: Clothes (dengan varian ukuran S/M/L/XL) dan Accessories (keychain, sticker, totebag)
- Filter produk berdasarkan rentang harga
- Live search suggestion (`/search`, rate-limited)
- Keranjang belanja berbasis session
- Checkout dengan hitung ongkos kirim otomatis (integrasi RajaOngkir: cari tujuan + hitung biaya)
- Riwayat & status pesanan customer (kalau login), termasuk edit profil dan hapus akun sendiri (pesanan lama tetap tersimpan buat rekap staff, cuma dilepas dari akunnya)
- Produk bisa dijadwalkan rilisnya (`published_at`) — otomatis muncul begitu waktunya tiba, tanpa perlu ubah manual
- Status stok produk otomatis sinkron (produk otomatis "habis" kalau stok/varian habis)
- Pesanan `pending` yang gak dibayar dalam waktu tertentu otomatis dibatalkan & dihapus (jalan "ambient" lewat middleware, gak butuh cron)
- Sitemap XML otomatis

**Dashboard staff**

- Landing dashboard beda konten sesuai role yang login (ringkasan berbeda buat owner/admin produk/staff pesanan)
- CRUD produk (upload multi-foto otomatis dikonversi ke WebP, atur varian ukuran & stok per kategori)
- Kelola status pesanan (update status: pending → processing → shipped → completed)
- Statistik pengunjung situs (device type, browser, halaman yang paling sering dibuka) — dicatat otomatis lewat middleware, tanpa package analytics eksternal
- Export data: pesanan (Excel), invoice pesanan (PDF), data produk (SQL), backup database & storage (khusus owner)
- Autentikasi staff terpisah dari customer (tabel `accounts`, bukan `users`), dengan rate limiting di endpoint login

## Tech Stack & Library

**Backend**

- [Laravel 13](https://laravel.com/docs) (PHP 8.3+)
- MySQL (lihat `.env` — `DB_CONNECTION=mysql`, database `mavnus_DB`)
- Laravel bawaan: Auth (tabel `users`, buat customer), Queue (database driver), Cache (database driver), Session (database driver)

**Package Composer tambahan**

- `barryvdh/laravel-dompdf` — generate PDF (invoice pesanan)
- `maatwebsite/excel` — export data ke Excel/CSV (data pesanan & produk)
- `intervention/image` + `intervention/image-laravel` — proses & convert gambar produk ke WebP
- `laravel/tinker` — REPL/debugging
- Dev only: `laravel/pail` (log viewer), `laravel/pint` (code style), `fakerphp/faker`, `mockery`, `phpunit`

**Frontend**

- Blade (server-rendered, gak pakai React/Vue)
- [Tailwind CSS v4](https://tailwindcss.com/) lewat `@tailwindcss/vite`
- [Vite](https://vitejs.dev/) sebagai build tool
- `bootstrap-icons` — icon set
- `sweetalert2` — dialog konfirmasi (misal konfirmasi logout, hapus produk)
- Vanilla JS (gak ada framework JS), style di-embed per halaman/komponen Blade

**Layanan eksternal**

- RajaOngkir API (lewat `App\Services\RajaOngkirService`) — pencarian tujuan & hitung ongkos kirim
- Pembayaran (Midtrans) — kolom sudah disiapkan di tabel `orders` (`midtrans_order_id`, `midtrans_transaction_id`) tapi integrasinya **belum diimplementasikan**; checkout saat ini masih placeholder/manual.
- Login Google (Laravel Socialite) — **belum diimplementasikan**. Tombol "Masuk dengan Google" di halaman login cuma UI (`href="#"`, gak nge-link kemana-mana), dan route `/login/google` + `/login/google/callback` udah didaftarin di `routes/web.php` tapi nunjuk ke method `redirectToGoogle`/`handleGoogleCallback` yang **gak ada** di `authController` — kalau route ini diakses langsung bakal error. Package `laravel/socialite` juga belum ke-install. `accountController` juga udah nyiapin logic yang ngecek `$user->google_id` (buat skip konfirmasi password pas edit/hapus akun), tapi kolom `google_id` itu sendiri belum ada di migration/tabel `users`, jadi kondisinya selalu `null` (dianggap akun non-Google). Kalau mau lanjutin fitur ini: install Socialite, tambah kolom `google_id` ke `users`, dan implementasikan dua method di atas.

## Setup

```bash
composer install
cp .env.example .env
```

Lalu isi kredensial MySQL di `.env` (`DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` — `.env.example` masih default ke SQLite, jadi bagian ini perlu diganti manual) dan buat database-nya, baru lanjut:

```bash
php artisan key:generate
php artisan migrate --seed
npm install
npm run dev
```

Env var tambahan yang dipakai kode tapi belum ada di `.env.example` (isi manual di `.env`):

- `RAJAONGKIR_API_KEY`, `RAJAONGKIR_BASE_URL`, `RAJAONGKIR_ORIGIN_ID` — wajib buat fitur hitung ongkir
- `ORDER_EXPIRE_MINUTES` (default 60) — batas waktu sebelum pesanan pending dibatalkan otomatis
- `ORDER_CLEANUP_THROTTLE_MINUTES` (default 5) — jarak minimal antar-jalan proses cleanup

Setelah migrate + seed, akun staff contoh (lihat `accountSeeder.php`, password sama untuk semua: `manage@mavnus`):

- `owner.mavnus` — role Owner
- `admin.mavnus` — role Admin Produk
- `staff.mavnus` — role Staff Pesanan

Login staff lewat `/crew-portal` (terpisah dari `/login` yang untuk customer).

## Struktur File

```
app/
├── Exports/
│   └── OrdersExport.php            # Definisi kolom & format export pesanan ke Excel
├── Http/
│   ├── Controllers/
│   │   ├── accountController.php       # Halaman akun customer: profil, edit, riwayat pesanan
│   │   ├── authController.php          # Register/login/logout customer (tabel users)
│   │   ├── cartController.php          # CRUD keranjang belanja (session-based)
│   │   ├── dashboardController.php     # Landing dashboard, beda konten tiap role
│   │   ├── homeController.php          # Beranda, listing clothes & accessories
│   │   ├── importExportController.php  # Semua endpoint export (Excel, PDF, SQL, backup)
│   │   ├── loginController.php         # Login/logout staff (tabel accounts, /crew-portal)
│   │   ├── orderController.php         # Checkout, pembuatan pesanan, kelola status pesanan
│   │   ├── productController.php       # CRUD produk (clothes & accessories) + detail produk
│   │   ├── searchController.php        # Live search suggestion
│   │   ├── ShippingController.php      # Integrasi RajaOngkir: cari tujuan & hitung ongkir
│   │   ├── SitemapController.php       # Generate sitemap.xml
│   │   └── visitorController.php       # Statistik pengunjung buat dashboard owner
│   └── Middleware/
│       ├── AutoCancelExpiredOrders.php # Trigger pembatalan pesanan expired tiap request
│       ├── cekLogin.php                # Guard halaman dashboard: wajib login staff
│       ├── cekRole.php                 # Guard halaman dashboard: batasi per role staff
│       └── TrackVisit.php              # Catat kunjungan halaman ke tabel visits
├── Models/
│   ├── accessoris.php              # Detail produk kategori accessories (tipe: keychain/sticker/totebag)
│   ├── account.php                 # Akun staff (owner/admin_produk/staff_pesanan)
│   ├── CartItem.php                # Item di keranjang belanja
│   ├── clothes.php                 # Detail produk kategori clothes (warna, material)
│   ├── Order.php                   # Pesanan customer
│   ├── OrderItem.php                # Item per pesanan (snapshot produk saat dibeli)
│   ├── product.php                 # Model produk utama (relasi ke clothes/accessories/varian/gambar)
│   ├── ProductImage.php             # Foto produk (multi-image, urut sesuai sort_order)
│   ├── ProductVariant.php           # Varian ukuran (S/M/L/XL) + stok, khusus clothes
│   ├── User.php                    # Akun customer (Laravel Auth bawaan)
│   └── Visit.php                   # Log kunjungan halaman (buat statistik)
├── Providers/
│   └── AppServiceProvider.php      # Service provider bawaan Laravel (belum ada kustomisasi)
├── Services/
│   └── RajaOngkirService.php       # Wrapper HTTP client ke API RajaOngkir
└── Support/
    └── OrderCleanup.php            # Logic pembatalan & hapus pesanan pending yang expired

database/
├── database.sqlite                 # Peninggalan setup awal (SQLite) — koneksi aktif sekarang MySQL, file ini gak dipakai
├── factories/
│   └── UserFactory.php             # Factory buat testing/seeding user
├── migrations/                     # Urut sesuai riwayat perubahan skema (users, accounts, products, dst.)
└── seeders/
    ├── accountSeeder.php           # Seed 3 akun staff contoh (owner, admin produk, staff pesanan)
    └── DatabaseSeeder.php          # Entry point seeding, panggil accountSeeder

resources/
├── css/app.css                     # Entry point CSS (import Tailwind)
├── js/app.js                       # Entry point JS
└── views/
    ├── components/                 # Komponen Blade yang dipakai berulang
    │   ├── account/                # bottom-nav & menu khusus halaman akun customer
    │   ├── dashboard/               # navbar, role-header, form field produk, modal edit, dsb
    │   │   └── field/               # Sub-komponen form input produk (dinamis per kategori)
    │   ├── errors/alerts.blade.php  # Komponen tampilan pesan error/success
    │   ├── banner.blade.php         # Banner beranda
    │   ├── merch.blade.php          # Grid produk unggulan di beranda
    │   ├── navbar.blade.php         # Navbar utama storefront
    │   ├── footer.blade.php         # Footer storefront
    │   ├── cart.blade.php           # Komponen isi keranjang
    │   ├── clothes.blade.php / accessoris.blade.php  # Card produk per kategori
    │   ├── product-detail.blade.php # Layout detail produk
    │   ├── filters.blade.php / price-filter-form.blade.php  # Filter harga listing produk
    │   └── pagination-light.blade.php  # Style pagination custom
    ├── errors/                     # Halaman error kustom (404, 429, 500, 503)
    ├── exports/
    │   └── orders-invoice.blade.php # Template invoice PDF pesanan
    ├── pages/
    │   ├── home.blade.php / clothes.blade.php / accessoris.blade.php / product_detail.blade.php  # Storefront
    │   ├── login.blade.php / register.blade.php / crew-login.blade.php  # Autentikasi
    │   ├── account.blade.php / account-edit.blade.php / account-orders.blade.php  # Akun customer
    │   ├── checkout.blade.php / order-success.blade.php  # Alur pemesanan
    │   ├── footer-info.blade.php    # Halaman info
    │   └── dashboard/                # Semua halaman staff (landing per role, produk, pesanan, visitor, import-export)
    ├── template/                    # Layout dasar (layout, bare-layout, account-layout, dashboard/layout)
    └── vendor/pagination/           # Override view pagination bawaan Laravel

routes/
├── console.php                     # Command Artisan kustom
└── web.php                         # Semua route web (storefront, akun, dashboard)

scripts/
└── patch-bootstrap-icons.cjs       # Script postinstall npm buat patch package bootstrap-icons

public/aset/                        # Gambar statis (logo, banner, gambar maintenance)
storage/app/public/products/        # Foto produk hasil upload (clothes & accessories)
```

## Catatan Hasil Pengecekan

Sudah dicek: semua controller, method, komponen Blade, halaman, middleware, service, dan model yang didaftarkan di atas **masih dipakai** — gak ada file/fungsi mati yang perlu dihapus. Sisa referensi ke "album" cuma ada di riwayat migration (`albums_migration.php`, `remove_album_category_and_add_stock.php`), itu memang riwayat perubahan skema, bukan kode aktif — aman dibiarkan.

Pengecualian: 2 route (`/login/google`, `/login/google/callback`) **terdaftar tapi nunjuk ke method yang gak ada** di `authController` — lihat catatan Login Google di bagian Layanan Eksternal di atas. Ini bukan dead code (route-nya aktif kalau diakses), tapi fitur setengah jadi yang bisa bikin error 500 kalau kepencet/diakses langsung.

Hal lain yang perlu diperhatikan (bukan dead code, tapi worth di-tracking):

- Integrasi pembayaran Midtrans belum diimplementasikan meski kolomnya sudah ada di tabel `orders` dan disebut sebagai placeholder di `checkout.blade.php`.
- Login Google belum diimplementasikan (lihat detail di atas) — kolom `google_id` yang dicek di `accountController` juga belum ada di skema `users`.
- `database/database.sqlite` masih ada di repo tapi gak terpakai karena koneksi database aktifnya MySQL — aman dihapus kalau mau beres-beres, atau dibiarkan sebagai fallback lokal.
