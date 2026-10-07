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

## Auto Deploy ke cPanel via GitHub Actions + SSH

Setiap `git push` ke branch `main`, GitHub otomatis build project lalu mengirim hasilnya ke hosting cPanel lewat SSH. Tidak perlu upload manual lewat File Manager lagi. Panduan ini ditulis supaya bisa dipakai ulang di project Laravel lain yang hostingnya cPanel.

### Cara kerjanya

1. GitHub menjalankan `composer install --no-dev` dan build aset Vite (`public/build` ada di `.gitignore`, jadi harus di-build di sini).
2. File dikirim ke server lewat `tar` yang dialirkan ke SSH. Server cPanel (Rumahweb) tidak punya `rsync`, jadi `rsync` tidak dipakai.
3. Lewat SSH, GitHub menjalankan perintah artisan di server: `migrate --force`, lalu cache config, route, dan view.

Yang **tidak** ditimpa di server: `.env`, `storage/`, `bootstrap/cache/` (dihapus lalu dibuat ulang oleh Laravel), `public/index.php`, dan `public/.htaccess`. File yang dihapus dari repo **tidak ikut terhapus** di server.

### Setup (sekali per project/hosting)

**1. Buat SSH key di laptop.** Jalankan di PowerShell (bukan di folder project, supaya private key tidak ikut ter-commit):

```powershell
mkdir $HOME\.ssh -Force
ssh-keygen -t ed25519 -f "$HOME\.ssh\nama_project_deploy" -N '""'
```

Tanda kutip `'""'` wajib persis begitu di PowerShell. Kalau ditulis `""` saja, `ssh-keygen` malah menampilkan daftar usage.

**2. Pasang public key di cPanel.**

1. Salin public key: `Get-Content "$HOME\.ssh\nama_project_deploy.pub" | Set-Clipboard`
2. cPanel → **SSH Access** → **Manage SSH Keys** → **Import Key**.
3. Isi nama key, tempel ke kolom **Public Key**, kolom private key dikosongkan, klik **Import**.
4. Kembali ke daftar Public Keys → **Manage** pada key tadi → **Authorize**. Status harus berubah menjadi _authorized_.

**3. Catat port SSH.** Port SSH hosting sering bukan 22 (di Rumahweb: `2223`). Cek di email hosting atau panduan SSH hostingnya.

**4. Isi GitHub secrets.** Salin private key: `Get-Content "$HOME\.ssh\nama_project_deploy" | Set-Clipboard`. Lalu buka repo → Settings → Secrets and variables → Actions → **New repository secret**:

| Secret            | Isi                                                                |
| ----------------- | ------------------------------------------------------------------ |
| `SSH_PRIVATE_KEY` | isi file private key lengkap, termasuk baris `BEGIN` dan `END`     |
| `SSH_HOST`        | hostname server cPanel                                             |
| `SSH_PORT`        | port SSH, misalnya `2223`                                          |
| `SSH_USER`        | username cPanel                                                    |
| `APP_PATH`        | folder aplikasi Laravel, misalnya `/home/<user>/nama_folder_app`   |
| `PUBLIC_PATH`     | document root domain, misalnya `/home/<user>/public_html/<domain>` |

Setelah private key tersimpan di GitHub, hapus file-nya dari laptop: `Remove-Item "$HOME\.ssh\nama_project_deploy"`.

**5. Cek server sebelum deploy pertama.**

- Backup folder `APP_PATH` dan `PUBLIC_PATH` lewat File Manager cPanel.
- Versi PHP di server harus memenuhi syarat `composer.lock`. Cek lewat terminal cPanel dengan `php -v`, atau lewat **MultiPHP Manager**.
- Buka `index.php` di `PUBLIC_PATH`. Path `vendor/autoload.php` dan `bootstrap/app.php` di dalamnya harus menunjuk ke `APP_PATH`.

**6. Pasang workflow.** Taruh `deploy.yml` di `.github/workflows/deploy.yml` (nama folder harus persis `.github` dan `workflows`), commit, lalu push ke `main`. Pantau hasilnya di tab **Actions** repo. Workflow juga bisa dijalankan manual lewat tombol **Run workflow**.

### Dipakai di project lain

Salin `.github/workflows/deploy.yml`, lalu sesuaikan:

- `php-version`: samakan dengan syarat di `composer.lock` (cek bagian `platform` / versi paket Symfony), bukan hanya `composer.json`.
- `node-version` dan perintah build. Di project ini `npx vite build` dipanggil langsung karena script `prebuild` merujuk file yang tidak ada di repo.
- Branch pemicu (`branches: [main]`). Untuk preview dan production yang berbeda, pakai dua workflow dengan branch dan secrets yang berbeda.
- Daftar `--exclude` di step upload, sesuai folder yang tidak boleh ditimpa di server.
- Buat key SSH baru dan secrets baru untuk tiap hosting.

### Troubleshooting

| Error di log Actions                                           | Penyebab dan solusi                                                                                                                               |
| -------------------------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------- |
| `Your lock file does not contain a compatible set of packages` | Versi PHP di workflow lebih rendah dari syarat `composer.lock`. Naikkan `php-version`.                                                            |
| `rsync: command not found`                                     | Server tidak punya rsync. Upload memakai `tar` lewat SSH (sudah begitu di workflow ini).                                                          |
| `Class "Laravel\Pail\PailServiceProvider" not found`           | Cache lama di `bootstrap/cache` masih memuat paket dev. Workflow menghapus `bootstrap/cache/*.php` sebelum menjalankan artisan.                   |
| `Permission denied (publickey)`                                | Key belum di-**Authorize** di cPanel, atau isi `SSH_PRIVATE_KEY` tidak lengkap.                                                                   |
| `Connection timed out`                                         | `SSH_PORT` salah, atau SSH dari luar diblokir hosting.                                                                                            |
| Koneksi SSH putus di tengah proses (timeout)                   | Firewall hosting memblokir IP GitHub, atau perintah lama tanpa output. Tambah `-o ServerAliveInterval=30 -o ConnectTimeout=20` di perintah `ssh`. |
| `php: command not found` / versi PHP server terlalu rendah     | Ubah versi PHP lewat MultiPHP Manager, atau isi path PHP spesifik (misalnya `/opt/cpanel/ea-php84/root/usr/bin/php`) di workflow.                 |

### Yang ter-upload dan yang tidak

| Ikut ter-upload otomatis                                            | Tidak ikut / tidak ditimpa                                  |
| ------------------------------------------------------------------- | ----------------------------------------------------------- |
| `app/`, `config/`, `database/`, `resources/`, `routes/`, `scripts/` | `.env` (selalu dipertahankan di server)                     |
| `vendor/` (tanpa paket dev)                                         | `storage/` (upload user, log, session, backup)              |
| `composer.json`, `composer.lock`, `artisan`, `bootstrap/app.php`    | `bootstrap/cache/` (dihapus lalu dibuat ulang oleh Laravel) |
| `public/build` (hasil build Vite), `public/aset`, `robots.txt`      | `public/index.php` dan `public/.htaccess`                   |
| File kecil lain di root (README, `package.json`, dll.)              | `.git`, `.github`, `node_modules`, `tests`                  |

Yang sering terlewat:

- Perubahan `.env` harus diedit **manual di server**. Kalau kode memakai variabel `.env` baru, tambahkan juga di `.env` server, kalau tidak aplikasi error.
- File yang dihapus dari repo **tidak ikut terhapus** di server.

### Kelebihan dan kekurangan

**Kelebihan**

- Tidak perlu upload manual lewat File Manager, jadi lebih cepat dan tidak ada file yang lupa terupload.
- Build selalu bersih dan sama setiap kali, tidak tergantung kondisi laptop.
- Ada riwayat setiap deploy di tab Actions, lengkap dengan log kalau gagal.
- Server tidak menyimpan kredensial GitHub, dan tidak butuh Composer atau Node.
- `.env` dan data di `storage/` tidak tersentuh.
- Bisa dipakai ulang di project lain.

**Kekurangan**

- Tidak ada rollback otomatis. Kalau deploy bermasalah, `git revert` lalu push lagi.
- Tidak ada test sebelum deploy, jadi kode yang rusak tetap terkirim.
- Tidak atomik: selama upload (sekitar 40 detik) situs bisa sebentar dalam kondisi setengah terupdate.
- Folder `vendor` dikirim ulang setiap deploy, jadi upload relatif lama.
- Migration jalan otomatis di setiap deploy.
- Bergantung pada GitHub, port SSH, dan hosting.

### Migrasi database

`php artisan migrate --force` jalan otomatis di setiap deploy, jadi perhatikan hal berikut.

**Umumnya aman:** tabel baru, kolom baru yang `nullable` atau punya `default`, index baru.

**Berisiko:** `dropColumn`, `dropTable`, `renameColumn`, ubah tipe kolom, kolom `NOT NULL` tanpa default di tabel yang sudah berisi data, atau index unik di kolom yang datanya masih duplikat.

Hal yang perlu diketahui:

- **Kode baru naik dulu, migrasi menyusul.** Di jeda itu, kode baru bisa membaca skema database yang masih lama. Migrasi yang hanya menambah kolom biasanya lolos, tapi yang menghapus atau mengganti nama kolom bisa membuat halaman error sebentar.
- **Tidak ada rollback otomatis.** Kalau migrasi gagal di tengah jalan, deploy berhenti. MySQL tidak bisa membatalkan perubahan struktur tabel di tengah migrasi, jadi tabel bisa tertinggal setengah berubah dan harus dibereskan manual lewat phpMyAdmin.
- **Database yang dipakai adalah yang ada di `.env` server.** Pastikan mengarah ke database yang benar (preview atau production).

Batasan dan kebiasaan yang disarankan:

- Jangan mengedit migrasi lama yang sudah pernah jalan. Selalu buat file migrasi baru.
- Backup database (export di phpMyAdmin) sebelum push migrasi yang mengubah atau menghapus data.
- Pecah perubahan berbahaya jadi dua kali deploy. Contoh ganti nama kolom: deploy pertama menambah kolom baru dan kode memakai keduanya, deploy kedua baru menghapus kolom lama.
- Uji di lokal dengan data yang mirip data asli.
- Tabel besar (puluhan ribu baris ke atas) bisa lama saat diubah. Hosting shared punya batas CPU dan proses, jadi proses yang terlalu lama bisa dimatikan paksa.
- Seeder tidak ikut jalan. Workflow hanya menjalankan `migrate`, bukan `db:seed`.
- Jangan pernah memasukkan `migrate:fresh` atau `migrate:rollback` ke workflow.

### Yang perlu diwaspadai

- **Migration yang merusak data.** Lihat bagian di atas.
- **Push ke `main` = langsung naik ke server.** Untuk production sungguhan, pakai workflow, branch, dan secrets terpisah dari preview.
- **Private key SSH.** Jangan di-commit atau dikirim lewat chat. Kalau sempat bocor, hapus key itu di cPanel (SSH Access → Manage) dan buat yang baru.
- **Versi PHP di hosting.** Kalau versi PHP domain diturunkan di cPanel (di bawah syarat `composer.lock`), situs bisa error 500 walau deploy hijau.
- **`APP_DEBUG` dan `.env` production.** Pastikan `APP_DEBUG=false` sebelum dipakai sungguhan.
- **Timeout SSH.** Bisa terjadi kalau IP GitHub diblokir firewall hosting, port SSH berubah, atau perintah artisan berjalan lama tanpa output.
- **Runner GitHub.** `ubuntu-latest` pindah ke Ubuntu 26 mulai 19 Oktober 2026. Untuk menghindari kejutan, ganti `runs-on: ubuntu-latest` menjadi `ubuntu-24.04`.

### Yang perlu dihindari

- Mengedit file kode langsung di server lewat File Manager. Perubahannya akan tertimpa deploy berikutnya. Edit di lokal, lalu push.
- Menghapus `.env` atau folder `storage` di server.
- Mengubah atau menghapus secrets di GitHub tanpa memperbarui sisi cPanel, dan sebaliknya.
- Lupa meng-commit `composer.lock`. Kalau tidak sesuai `composer.json`, composer gagal di Actions.
- Meng-commit private key atau `.env`.
