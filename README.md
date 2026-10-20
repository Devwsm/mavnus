# Mavnus

Toko online merchandise resmi **Whisnu Santika**. Pembeli memilih produk, memasukkannya ke keranjang, lalu memesan **tanpa wajib punya akun**. Tim punya dashboard sendiri untuk mengelola produk, stok, dan pesanan.

> **Status:** sudah berjalan di server. Tim memakai alamat preview (subdomain) untuk review, dan nanti dipindah ke domain utama. **Pembayaran online belum aktif** (lihat [Belum tersedia](#belum-tersedia)).

## Daftar isi

1. [Fitur](#1-fitur)
2. [Panduan praktis (tanpa coding)](#2-panduan-praktis-tanpa-coding)
3. [Istilah singkat](#3-istilah-singkat)
4. [Struktur file (untuk developer)](#4-struktur-file-untuk-developer)
5. [Menjalankan di komputer sendiri](#5-menjalankan-di-komputer-sendiri)
6. [Auto deploy ke production](#6-auto-deploy-ke-production)
7. [Pengaturan server production](#7-pengaturan-server-production)
8. [Checklist pindah ke domain utama](#8-checklist-pindah-ke-domain-utama)

---

## 1. Fitur

### Untuk pembeli

- **Dua kategori produk:** Clothes/Pakaian (ukuran S, M, L, XL) dan Accessories/Aksesori (keychain, sticker, totebag).
- **Dua bahasa:** Indonesia dan Inggris. Pembeli memilih lewat tombol bendera di menu atas atau footer, dan pilihannya diingat 1 tahun. Bahasa awalnya Indonesia.
- **Cari produk dan filter harga.** Saran produk muncul langsung saat mengetik.
- **Keranjang belanja** tanpa perlu login.
- **Checkout dengan ongkos kirim otomatis** (JNE, J&T, SiCepat lewat RajaOngkir). Harga, ongkir, dan total dihitung ulang oleh server, jadi tidak bisa diubah dari browser. Stok langsung berkurang, sehingga produk tidak bisa terjual melebihi stok.
- **Akun pembeli (opsional):** lihat riwayat dan status pesanan, ubah profil, dan hapus akun (wajib memasukkan password). Pesanan tetap tersimpan untuk rekap toko walau akunnya dihapus, tetapi tidak lagi terhubung ke akun.
- **Halaman "pesanan berhasil"** hanya bisa dibuka oleh pembeli yang membuat pesanan itu.
- **Jadwal rilis produk:** produk tayang otomatis di waktu tertentu (maksimal 7 hari ke depan). Produk otomatis ditandai habis saat stok kosong.
- **Pembatalan otomatis:** pesanan yang belum diproses dibatalkan setelah 60 menit dan stoknya dikembalikan. Pesanannya tidak hilang, tetap tercatat dengan label **Kedaluwarsa**.
- **Email konfirmasi pesanan** (opsional, bawaannya mati).
- **Newsletter** lewat footer, serta halaman info/bantuan, sitemap, dan robots untuk mesin pencari.

### Untuk tim (dashboard)

Login tim ada di `/crew-portal`, terpisah dari login pembeli. Dashboard hanya tersedia dalam Bahasa Indonesia.

| Role              | Yang bisa dilakukan                                                          |
| ----------------- | ---------------------------------------------------------------------------- |
| **Owner**         | Semuanya, plus ringkasan omzet, statistik pengunjung, dan backup data.       |
| **Admin Produk**  | Tambah, ubah, hapus produk; atur foto, ukuran, dan stok; export data produk. |
| **Staff Pesanan** | Lihat pesanan dan ubah statusnya; export pesanan dan invoice.                |

- **Daftar pesanan:** bisa dicari lewat nomor pesanan, nama, nomor HP, atau email, dan difilter berdasarkan status dan tanggal.
- **Status pesanan:** menunggu → diproses → dikirim → selesai. Pesanan bisa dibatalkan sebelum dikirim (stok dikembalikan). Status boleh dikoreksi mundur jika salah klik, tetapi pesanan yang sudah dibatalkan terkunci.
- **Foto produk:** maksimal 8 foto per produk, 5 MB per foto. Otomatis dikecilkan dan diubah ke WebP supaya halaman ringan.
- **Ringkasan owner:** omzet 6 bulan terakhir, omzet per kategori bulan ini, dan produk terlaris. Omzet hanya dihitung dari pesanan yang sudah selesai.
- **Statistik pengunjung:** perangkat, browser, dan halaman terpopuler, tanpa layanan analitik pihak luar. Alamat IP pengunjung ikut tercatat, jadi sesuaikan kebijakan privasi bila perlu.
- **Invoice dan export:** invoice satu pesanan (lihat atau PDF), export pesanan (Excel), rekap invoice (PDF), serta data produk dan pesanan (SQL). Khusus Owner: backup database dan backup foto (zip).

### Keamanan

Percobaan login dibatasi. Keranjang, pencarian, checkout, dan newsletter dibatasi jumlah permintaannya agar tidak disalahgunakan. Semua halaman memakai header keamanan standar, dan koneksi ke layanan ongkir memverifikasi sertifikat SSL.

### Belum tersedia

| Fitur                            | Keterangan                                                                                                                                                                                                                                                                             |
| -------------------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Pembayaran online (Midtrans)** | Menunggu persetujuan. Setelah checkout, pesanan tercatat "belum dibayar". **Penting:** selama pembayaran belum aktif, pesanan "menunggu" otomatis menjadi "Kedaluwarsa" setelah 60 menit. Staff harus memprosesnya sebelum itu, atau perpanjang waktunya lewat `ORDER_EXPIRE_MINUTES`. |
| **Login dengan Google**          | Ditunda karena rumit dan berisiko untuk akun pembeli.                                                                                                                                                                                                                                  |
| **Lupa password**                | Tautannya sudah tampil di halaman login, tetapi fiturnya belum dibuat.                                                                                                                                                                                                                 |
| **Kelola akun tim**              | Akun tim bisa dinonaktifkan lewat database, belum ada halamannya.                                                                                                                                                                                                                      |
| **Nama produk dua bahasa**       | Nama dan deskripsi produk hanya satu bahasa (sesuai yang diketik admin), karena tersimpan di database.                                                                                                                                                                                 |
| **Banner dua bahasa**            | Teks di dalam gambar banner tidak bisa diterjemahkan otomatis. Perlu satu gambar per bahasa (lihat [panduan banner](#mengganti-banner)). Selama belum ada, kedua bahasa memakai banner yang sama.                                                                                      |

---

## 2. Panduan praktis (tanpa coding)

Semua perubahan di bawah naik ke server otomatis setelah di-push ke GitHub (lihat [bagian 6](#6-auto-deploy-ke-production)). Pekerjaan manual di server (hapus file lama, ganti password akun tim, dan lainnya) ada di [bagian 6](#yang-harus-dilakukan-manual).

### Mengganti banner

Banner di beranda memilih gambar sesuai bahasa pengunjung. Taruh gambar di `public/aset/banner/` dengan nama persis seperti ini:

| Bahasa        | Nama file (cukup salah satu)                                      |
| ------------- | ----------------------------------------------------------------- |
| Indonesia     | `banner-id.jpg` atau `banner-id-1600.webp` + `banner-id-800.webp` |
| Inggris       | `banner-en.jpg` atau `banner-en-1600.webp` + `banner-en-800.webp` |
| Cadangan umum | `banner.jpg` (dipakai jika file bahasa belum ada)                 |

- Ukuran yang dipakai **1600 × 400 piksel**. Versi 800 px (untuk HP) bersifat opsional.
- **Pakai huruf kecil semua.** Server membedakan huruf besar dan kecil, jadi `Banner.jpg` tidak dikenali sebagai `banner.jpg`.
- Gambar disimpan lama di browser pengunjung. Kalau banner tidak berubah setelah deploy, tekan **Ctrl + F5**.

### Mengubah teks atau terjemahan

Teks tampilan toko ada di folder `lang/`: `lang/id/` untuk Indonesia dan `lang/en/` untuk Inggris. Isinya dibagi per topik (`site`, `auth`, `account`, `checkout`, `order`, `info`, `validation`, `pagination`).

- Ubah teks di **kedua** folder supaya sama-sama diperbarui.
- Untuk halaman toko, jangan mengetik teks langsung di file tampilan. Selalu lewat folder `lang/`. (Dashboard tim memang Indonesia saja dan teksnya langsung di file tampilan.)
- Sebelum push, jalankan `php artisan test`. Tes akan gagal kalau ada teks yang hanya ada di satu bahasa, jadi terjemahan yang kurang ketahuan lebih awal.

### Pengaturan yang sering diubah

Diubah di file pengaturan server (`.env`, lihat [bagian 7](#7-pengaturan-server-production)). Setelah mengubahnya, jalankan ulang deploy lewat **Actions → Run workflow**.

| Ingin...                                  | Ubah                                                                            |
| ----------------------------------------- | ------------------------------------------------------------------------------- |
| Memperpanjang waktu sebelum pesanan batal | `ORDER_EXPIRE_MINUTES` (menit, bawaan 60)                                       |
| Mengaktifkan email konfirmasi pesanan     | `ORDER_EMAIL_ENABLED=true`, setelah pengaturan email (`MAIL_*`) diisi dan diuji |
| Mengatur lama keranjang disimpan          | `CART_RETENTION_DAYS` (hari, bawaan 30)                                         |

---

## 3. Istilah singkat

| Istilah            | Artinya                                                                                                |
| ------------------ | ------------------------------------------------------------------------------------------------------ |
| **Push**           | Mengirim perubahan dari komputer ke GitHub.                                                            |
| **Deploy**         | Menaikkan hasil terbaru ke server agar tampil di situs. Di project ini otomatis setiap push ke `main`. |
| **GitHub Actions** | "Robot" GitHub yang menjalankan deploy. Hasilnya bisa dilihat di tab **Actions** di halaman repo.      |
| **Branch `main`**  | Jalur utama kode. Apa pun yang masuk ke sini langsung naik ke server.                                  |
| **`.env`**         | File pengaturan rahasia per komputer atau server (database, kunci layanan). Tidak ikut ke GitHub.      |
| **Migrasi**        | Pembaruan struktur tabel database. Berjalan otomatis saat deploy.                                      |
| **Secret**         | Data rahasia yang disimpan di pengaturan GitHub (kunci SSH, alamat server), dipakai oleh deploy.       |
| **Seeder**         | Perintah yang mengisi data awal, misalnya 3 akun tim.                                                  |

---

## 4. Struktur file (untuk developer)

Dibangun dengan **Laravel 13** (PHP 8.4.1 atau lebih baru), MySQL, Tailwind CSS v4, dan Vite. Alurnya: pengunjung membuka alamat di `routes/web.php`, diarahkan ke _controller_, yang mengambil data dari _model_ (database) dan menampilkannya lewat _view_ (halaman).

<details>
<summary><strong>Controller</strong> (<code>app/Http/Controllers/</code>): pengatur halaman</summary>

| File                         | Fungsi                                                                                         |
| ---------------------------- | ---------------------------------------------------------------------------------------------- |
| `homeController.php`         | Beranda, daftar Clothes dan Accessories (dengan filter harga), halaman info.                   |
| `productController.php`      | Detail produk untuk pembeli, dan tambah/ubah/hapus produk untuk tim.                           |
| `searchController.php`       | Saran produk saat mengetik di kolom cari.                                                      |
| `cartController.php`         | Tambah, ubah, hapus isi keranjang.                                                             |
| `orderController.php`        | Checkout, pembuatan pesanan, halaman sukses, kelola dan ubah status pesanan, email konfirmasi. |
| `ShippingController.php`     | Cari kota tujuan dan hitung ongkir.                                                            |
| `authController.php`         | Daftar, masuk, dan keluar untuk pembeli.                                                       |
| `accountController.php`      | Akun pembeli: profil, riwayat pesanan, hapus akun.                                             |
| `loginController.php`        | Masuk dan keluar untuk tim di `/crew-portal`.                                                  |
| `dashboardController.php`    | Halaman utama dashboard, isinya menyesuaikan role.                                             |
| `visitorController.php`      | Statistik pengunjung.                                                                          |
| `importExportController.php` | Export Excel/PDF/SQL, invoice, dan backup.                                                     |
| `NewsletterController.php`   | Menyimpan email pelanggan newsletter.                                                          |
| `SitemapController.php`      | Membuat `sitemap.xml` dan `robots.txt` secara otomatis.                                        |
| `LocaleController.php`       | Ganti bahasa (Indonesia/Inggris).                                                              |
| `Controller.php`             | Dasar bawaan Laravel.                                                                          |

</details>

<details>
<summary><strong>Middleware</strong> (<code>app/Http/Middleware/</code>): penjaga di setiap permintaan</summary>

| File                          | Fungsi                                                                                          |
| ----------------------------- | ----------------------------------------------------------------------------------------------- |
| `SetLocale.php`               | Menentukan bahasa tampilan (pilihan pembeli, atau bawaan Indonesia).                            |
| `TrackVisit.php`              | Mencatat kunjungan halaman untuk statistik.                                                     |
| `AutoCancelExpiredOrders.php` | Membatalkan pesanan kedaluwarsa dan membersihkan keranjang lama, tanpa jadwal khusus di server. |
| `SecurityHeaders.php`         | Menambahkan header keamanan di semua halaman.                                                   |
| `cekLogin.php`                | Memastikan anggota tim sudah masuk dan akunnya masih aktif.                                     |
| `cekRole.php`                 | Membatasi halaman sesuai role tim.                                                              |

</details>

<details>
<summary><strong>Model</strong> (<code>app/Models/</code>): data di database</summary>

| File                                     | Isi data                                                            |
| ---------------------------------------- | ------------------------------------------------------------------- |
| `product.php`                            | Produk (nama, harga, berat, stok, jadwal rilis).                    |
| `clothes.php`, `accessoris.php`          | Detail khusus: warna dan bahan, atau tipe aksesoris.                |
| `ProductVariant.php`, `ProductImage.php` | Ukuran dan stok, serta foto produk.                                 |
| `CartItem.php`                           | Isi keranjang.                                                      |
| `Order.php`, `OrderItem.php`             | Pesanan dan barang di dalamnya (harga disimpan sesuai saat dibeli). |
| `User.php`                               | Akun pembeli.                                                       |
| `account.php`                            | Akun tim (owner, admin produk, staff pesanan).                      |
| `Visit.php`                              | Catatan kunjungan.                                                  |
| `NewsletterSubscriber.php`               | Email pelanggan newsletter.                                         |

</details>

<details>
<summary><strong>Pendukung</strong> di <code>app/</code></summary>

| File                                 | Fungsi                                                               |
| ------------------------------------ | -------------------------------------------------------------------- |
| `Services/RajaOngkirService.php`     | Menghubungi RajaOngkir untuk cari tujuan dan hitung ongkir.          |
| `Exceptions/RajaOngkirException.php` | Pesan error yang aman ditampilkan jika layanan ongkir bermasalah.    |
| `Exports/OrdersExport.php`           | Susunan file Excel untuk export pesanan.                             |
| `Mail/OrderPlaced.php`               | Email konfirmasi pesanan.                                            |
| `Support/CartSession.php`            | Menandai keranjang milik pengunjung agar tidak hilang saat login.    |
| `Support/OrderCleanup.php`           | Aturan pembatalan pesanan kedaluwarsa dan pengembalian stok.         |
| `Support/Img.php`                    | Memilih gambar versi ringan (WebP), atau gambar asli jika tidak ada. |
| `Providers/AppServiceProvider.php`   | Pengaturan awal aplikasi (https dan jumlah isi keranjang).           |

</details>

<details>
<summary><strong>Folder dan file lainnya</strong></summary>

| Lokasi                                           | Fungsi                                                                                                    |
| ------------------------------------------------ | --------------------------------------------------------------------------------------------------------- |
| `routes/web.php`                                 | Daftar semua alamat halaman dan siapa yang boleh membukanya.                                              |
| `config/mavnus.php`                              | Pengaturan khas toko: bahasa tersedia, batas waktu pesanan, retensi keranjang, email.                     |
| `config/services.php`                            | Pengaturan RajaOngkir. File `config/` lain adalah bawaan Laravel.                                         |
| `database/migrations/`                           | Riwayat pembuatan dan perubahan tabel database.                                                           |
| `database/seeders/`                              | Pembuat 3 akun tim awal (satu per role).                                                                  |
| `lang/id/`, `lang/en/`                           | Terjemahan per topik (lihat [mengubah teks](#mengubah-teks-atau-terjemahan)).                             |
| `resources/views/pages/`                         | Halaman utuh: beranda, produk, checkout, akun, login, info, dan halaman dashboard.                        |
| `resources/views/components/`                    | Potongan tampilan yang dipakai ulang: menu atas, banner, footer, keranjang, filter, tombol bahasa.        |
| `resources/views/template/`                      | Kerangka halaman: layout pembeli, layout akun, layout dashboard.                                          |
| `resources/views/emails/`, `exports/`, `errors/` | Email, invoice PDF, dan halaman error (404, 429, 500, 503).                                               |
| `resources/css/`, `js/`                          | Gaya tampilan (Tailwind) dan skrip pendukung.                                                             |
| `public/`                                        | Aset yang bisa diakses langsung (logo, banner, `index.php`). Folder `build/` dibuat otomatis saat deploy. |
| `scripts/`                                       | Pembuat font ikon.                                                                                        |
| `tests/Feature/`                                 | Tes otomatis: akun, keranjang, checkout, status pesanan, produk, akses tim, tampilan toko, dan bahasa.    |
| `.github/workflows/deploy.yml`                   | Resep auto deploy ke hosting.                                                                             |
| `.env.example`                                   | Contoh pengaturan untuk komputer sendiri.                                                                 |

</details>

---

## 5. Menjalankan di komputer sendiri

Butuh PHP 8.4+, Composer, Node.js 22, dan MySQL.

```bash
composer install
npm install
cp .env.example .env        # Windows: copy .env.example .env
php artisan key:generate
# isi .env (database dan kunci RajaOngkir), lalu:
php artisan migrate --seed  # buat tabel + 3 akun tim contoh
npm run icons               # buat font ikon
npx vite                    # terminal 1: tampilan
php artisan serve           # terminal 2: situs
```

> `npm run dev` dan `npm run build` saat ini error karena skrip `scripts/optimize-images.cjs` tidak ada. Pakai `npx vite` dan `npx vite build`. Situs tetap normal dengan gambar asli.

**Tes otomatis:** `php artisan test` (memakai database sementara, data MySQL aman).

---

## 6. Auto deploy ke production

### Apa yang terjadi saat push

Setiap `git push` ke branch `main`, GitHub otomatis menyiapkan project dan mengirimnya ke hosting. Tidak perlu upload manual.

**Ini bukan sinkronisasi.** Deploy **menimpa dan menambah**: file di GitHub dikirim ulang dan menimpa file server dengan nama sama, file baru ditambahkan. **File yang dihapus dari GitHub tidak ikut terhapus di server.**

Urutannya:

1. GitHub menyiapkan project: memasang library dan membuat CSS, JS, dan ikon.
2. Mengirim file aplikasi ke folder aplikasi di server (`APP_PATH`).
3. Mengirim file tampilan publik ke folder publik domain (`PUBLIC_PATH`).
4. Menjalankan perintah di server: membersihkan cache lama, memperbarui struktur database (migrasi), menyambungkan folder foto, dan menyimpan ulang pengaturan.
5. Menghapus kunci SSH sementara.

Hasilnya terlihat di tab **Actions**. Deploy juga bisa dijalankan manual lewat **Run workflow**. File dikirim dengan `tar` karena server tidak punya `rsync`.

### Yang terkirim dan yang tidak

| Terkirim dan ditimpa setiap push                                                                                                                                                      | Tidak disentuh deploy                                                                                                                                                                |
| ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| Kode aplikasi (`app`, `config`, `routes`, `lang`, `resources`, `database`), library (`vendor`), hasil build tampilan (`public/build`), logo dan banner (`public/aset`), `favicon.ico` | `.env`, folder `storage/` (foto produk, log, sesi), `public/index.php`, `public/.htaccess`, **isi database** (hanya struktur yang diperbarui lewat migrasi; seeder tidak ikut jalan) |

Tidak pernah dikirim: `.git`, `.github`, `tests`, `node_modules`.

### Yang harus dilakukan manual

| Hal                                 | Penjelasan                                                                                                                                                                                                                       |
| ----------------------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Mengubah `.env` di server**       | Server menyimpan salinan pengaturan setiap deploy, jadi perubahan baru terbaca setelah deploy berikutnya. Setelah mengedit `.env`, jalankan ulang lewat **Actions → Run workflow**.                                              |
| **`index.php` di server (penting)** | File `index.php` di `PUBLIC_PATH` disesuaikan sekali saat setup dan tidak ditimpa deploy. Isinya **wajib** memuat baris `$app->usePublicPath(__DIR__);` (lihat kotak di bawah tabel). Wajib diulang saat pindah ke domain utama. |
| **`.htaccess` di server**           | Disesuaikan sekali saat setup dan tidak ditimpa deploy.                                                                                                                                                                          |
| **Menghapus file di server**        | Lewat cPanel File Manager (misalnya `robots.txt` lama).                                                                                                                                                                          |
| **Password akun tim**               | Ganti password bawaan seeder lewat database sebelum dipakai sungguhan.                                                                                                                                                           |
| **Backup database**                 | Lakukan lewat phpMyAdmin sebelum push yang mengubah struktur tabel.                                                                                                                                                              |
| **Versi PHP di cPanel**             | Harus 8.4.1 atau lebih baru (MultiPHP Manager).                                                                                                                                                                                  |

**Isi `index.php` di server.** Tambahkan baris `usePublicPath` tepat setelah baris `$app = require_once ...`:

```php
$app = require_once /* baris bawaan yang sudah ada, jangan diubah */;
$app->usePublicPath(__DIR__);   // tambahkan baris ini
$app->handleRequest(Request::capture());
```

Tanpa baris ini, Laravel mencari hasil build di folder yang salah, sehingga halaman memakai CSS lama dan tampilan di server berbeda dari local (ikon dan footer berantakan).

### Setup sekali per hosting

1. **Buat SSH key** di laptop (di luar folder project). PowerShell:
    ```powershell
    ssh-keygen -t ed25519 -f "$HOME\.ssh\nama_project_deploy" -N '""'
    ```
2. **Pasang public key di cPanel:** SSH Access → Manage SSH Keys → Import Key, lalu **Authorize**.
3. **Catat port SSH** (sering bukan 22).
4. **Isi GitHub secrets** (repo → Settings → Secrets and variables → Actions). Setelah tersimpan, hapus file private key dari laptop.

    | Secret            | Isi                                                        |
    | ----------------- | ---------------------------------------------------------- |
    | `SSH_PRIVATE_KEY` | Isi private key lengkap (termasuk baris `BEGIN` dan `END`) |
    | `SSH_HOST`        | Alamat server cPanel                                       |
    | `SSH_PORT`        | Port SSH                                                   |
    | `SSH_USER`        | Username cPanel                                            |
    | `APP_PATH`        | Folder aplikasi Laravel di server                          |
    | `PUBLIC_PATH`     | Folder publik (document root) domain                       |

5. **Cek server:** backup `APP_PATH` dan `PUBLIC_PATH`, pastikan PHP 8.4.1+, dan pastikan `index.php` di `PUBLIC_PATH` menunjuk ke `APP_PATH` serta memuat baris `usePublicPath` di atas.
6. **Pasang workflow** `.github/workflows/deploy.yml`, commit, lalu push ke `main`.

Untuk project Laravel lain: salin `deploy.yml`, sesuaikan versi PHP dan Node, perintah build, branch, dan daftar `--exclude`, lalu buat SSH key dan secrets baru untuk tiap hosting.

### Kalau bermasalah

| Gejala                                                                | Penyebab dan solusi                                                                                                                                      |
| --------------------------------------------------------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Tampilan di server berbeda dari local (ikon rapat, footer satu kolom) | Tambahkan `$app->usePublicPath(__DIR__);` ke `index.php` server, lalu jalankan `php artisan optimize:clear`. Cek juga deploy di tab Actions sudah hijau. |
| Banner atau gambar tidak muncul                                       | Nama file tidak persis sama (huruf besar/kecil), atau file belum ikut terkirim. Pakai huruf kecil semua.                                                 |
| Situs error 500 padahal deploy hijau                                  | Versi PHP domain di cPanel turun, atau `.env` server belum punya variabel baru.                                                                          |
| `Your lock file does not contain a compatible set of packages`        | Versi PHP di workflow terlalu rendah. Naikkan `php-version` (minimal 8.4).                                                                               |
| `Permission denied (publickey)`                                       | Key belum di-**Authorize** di cPanel, atau isi `SSH_PRIVATE_KEY` tidak lengkap.                                                                          |
| `Connection timed out`                                                | `SSH_PORT` salah, atau hosting memblokir koneksi dari GitHub.                                                                                            |

### Hal penting

- **Push ke `main` = langsung naik ke server.** Tidak ada tes otomatis di alur deploy dan tidak ada rollback otomatis. Jalankan `php artisan test` sebelum push. Jika bermasalah, `git revert` lalu push lagi.
- **Migrasi database jalan otomatis** di setiap deploy. Aman untuk tabel atau kolom baru, berisiko untuk menghapus atau mengganti nama kolom. Jangan edit migrasi lama, buat migrasi baru.
- **Jangan edit kode langsung di server.** Akan tertimpa deploy berikutnya.
- **Jangan commit atau kirim private key lewat chat.** Jika bocor, hapus key di cPanel dan buat yang baru.
- **Upload tidak atomik:** selama sekitar 40 detik situs bisa sebentar setengah terupdate.
- **Runner GitHub:** `ubuntu-latest` mulai bertahap pindah ke Ubuntu 26.04 antara 19 Oktober dan 19 November 2026. Agar aman, ganti `runs-on` di workflow menjadi `ubuntu-24.04`.

---

## 7. Pengaturan server production

Isi `.env` di server (bukan di GitHub). Nilai rahasia jangan ditulis di dokumen ini.

| Pengaturan                                                          | Nilai di production                                       |
| ------------------------------------------------------------------- | --------------------------------------------------------- |
| `APP_ENV`                                                           | `production`                                              |
| `APP_DEBUG`                                                         | `false`                                                   |
| `APP_URL`                                                           | Alamat situs dengan `https://`, persis seperti di browser |
| `APP_LOCALE`                                                        | `id`                                                      |
| `SESSION_SECURE_COOKIE`                                             | `true` (hanya di server, jangan di local)                 |
| `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`              | Data database hosting                                     |
| `RAJAONGKIR_API_KEY`, `RAJAONGKIR_BASE_URL`, `RAJAONGKIR_ORIGIN_ID` | Data akun RajaOngkir (wajib agar ongkir jalan)            |
| `MAIL_*`, `ORDER_EMAIL_ENABLED`                                     | Isi dan uji dulu sebelum email konfirmasi diaktifkan      |

Daftar lengkap variabel ada di `.env.example`.

---

## 8. Checklist pindah ke domain utama

- [ ] Cocokkan `.env` hosting dengan [tabel pengaturan production](#7-pengaturan-server-production).
- [ ] Siapkan `APP_PATH` dan `PUBLIC_PATH` untuk domain utama, lalu ubah secret-nya di GitHub.
- [ ] Pastikan `index.php` domain utama memuat `$app->usePublicPath(__DIR__);`.
- [ ] Hapus `robots.txt` lama di server (yang dipakai adalah buatan otomatis aplikasi).
- [ ] Ganti password akun tim bawaan seeder.
- [ ] Siapkan banner Indonesia dan Inggris ([panduan banner](#mengganti-banner)).
- [ ] Jalankan ulang workflow setelah `.env` diubah, lalu uji checkout dan ongkir di alamat baru.
- [ ] Putuskan cara menangani pesanan selama pembayaran online belum aktif (pesanan kedaluwarsa dalam 60 menit).
