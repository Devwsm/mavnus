# Mavnus

## 1. Apa itu Mavnus?

Mavnus adalah **toko online merchandise resmi Whisnu Santika**. Pembeli bisa memilih produk, memasukkannya ke keranjang, lalu memesan **tanpa wajib punya akun**. Di sisi lain, tim punya dashboard sendiri untuk mengelola produk, stok, dan pesanan.

**Status saat ini:** sudah berjalan di server. Tim memakai alamat preview (subdomain) untuk review, dan nanti akan dipindah ke domain utama. Pembayaran online belum aktif (lihat [Belum tersedia](#belum-tersedia)).

## 2. Fitur sejauh ini

### Untuk pembeli

- **Dua kategori produk:** Clothes (ukuran S, M, L, XL) dan Accessories (keychain, sticker, totebag).
- **Dua bahasa:** Indonesia dan Inggris. Pembeli memilih lewat tombol bendera di menu atas atau footer. Pilihannya diingat selama 1 tahun. Bahasa awalnya Indonesia.
- **Cari produk dan filter harga.** Saat mengetik di kolom cari, saran produk langsung muncul.
- **Keranjang belanja** tanpa perlu login.
- **Checkout dengan ongkos kirim otomatis** (JNE, J&T, SiCepat lewat RajaOngkir). Harga, ongkir, dan total dihitung ulang oleh server, jadi tidak bisa diubah dari browser. Stok langsung berkurang, sehingga produk tidak bisa terjual melebihi stok.
- **Akun pembeli (opsional):** lihat riwayat dan status pesanan, ubah profil, dan hapus akun (wajib memasukkan password). Pesanan tetap tersimpan untuk rekap toko walau akunnya dihapus, tetapi tidak lagi terhubung ke akun.
- **Halaman "pesanan berhasil"** hanya bisa dibuka oleh pembeli yang membuat pesanan itu.
- **Jadwal rilis produk:** produk bisa ditayangkan otomatis di waktu tertentu (maksimal 7 hari ke depan). Produk otomatis ditandai habis saat stok kosong.
- **Pembatalan otomatis:** pesanan yang belum diproses dibatalkan setelah 60 menit dan stoknya dikembalikan. Pesanannya tidak hilang, tetap tercatat dengan label **Kedaluwarsa**.
- **Email konfirmasi pesanan** (opsional, bawaannya mati, diaktifkan lewat pengaturan `ORDER_EMAIL_ENABLED`).
- **Berlangganan newsletter** lewat footer.
- **Halaman info/bantuan**, serta sitemap dan robots untuk mesin pencari.

### Untuk tim (dashboard)

Login tim ada di `/crew-portal` dan terpisah dari login pembeli. Dashboard hanya tersedia dalam Bahasa Indonesia.

| Role              | Yang bisa dilakukan                                                          |
| ----------------- | ---------------------------------------------------------------------------- |
| **Owner**         | Semuanya, plus ringkasan omzet, statistik pengunjung, dan backup data.       |
| **Admin Produk**  | Tambah, ubah, hapus produk; atur foto, ukuran, dan stok; export data produk. |
| **Staff Pesanan** | Lihat pesanan dan ubah statusnya; export pesanan dan invoice.                |

- **Daftar pesanan:** bisa dicari lewat nomor pesanan, nama, nomor HP, atau email, dan difilter berdasarkan status dan tanggal.
- **Status pesanan:** menunggu → diproses → dikirim → selesai. Pesanan bisa dibatalkan sebelum dikirim (stok dikembalikan). Status boleh dikoreksi mundur jika salah klik, tetapi pesanan yang sudah dibatalkan terkunci.
- **Foto produk:** maksimal 8 foto per produk, 5 MB per foto. Otomatis dikecilkan dan diubah ke format WebP supaya halaman ringan.
- **Ringkasan owner:** omzet 6 bulan terakhir, omzet per kategori bulan ini, dan produk terlaris. Omzet hanya dihitung dari pesanan yang sudah selesai.
- **Statistik pengunjung:** perangkat, browser, dan halaman terpopuler, tanpa layanan analitik pihak luar. Alamat IP pengunjung ikut tercatat, jadi sesuaikan kebijakan privasi bila perlu.
- **Invoice dan export:** invoice satu pesanan (lihat atau PDF), export pesanan (Excel), rekap invoice (PDF), serta data produk dan pesanan (SQL). Khusus Owner: backup database dan backup foto (zip).

### Keamanan

Percobaan login dibatasi. Keranjang, pencarian, checkout, dan newsletter dibatasi jumlah permintaannya agar tidak disalahgunakan. Semua halaman dikirim dengan header keamanan standar, dan koneksi ke layanan ongkir memverifikasi sertifikat SSL.

### Belum tersedia

- **Pembayaran online (Midtrans).** Menunggu persetujuan. Setelah checkout, pesanan tercatat "belum dibayar". **Penting:** selama pembayaran belum aktif, pesanan "menunggu" otomatis menjadi "Kedaluwarsa" setelah 60 menit. Staff harus memproses pesanan sebelum itu, atau perpanjang waktunya lewat `ORDER_EXPIRE_MINUTES`.
- **Login dengan Google.** Ditunda karena rumit dan berisiko untuk akun pembeli.
- **Lupa password.** Tautannya sudah tampil di halaman login, tetapi fiturnya belum dibuat.
- **Halaman kelola akun tim.** Akun tim bisa dinonaktifkan lewat database, belum ada halamannya.
- **Nama dan deskripsi produk** hanya satu bahasa (sesuai yang diketik admin), karena tersimpan di database.

## 3. Struktur dan arsitektur file

Dibangun dengan **Laravel 13** (PHP 8.4.1 atau lebih baru), MySQL, Tailwind CSS v4, dan Vite. Pola kerjanya: pengunjung membuka alamat di `routes/web.php`, lalu diarahkan ke _controller_, yang mengambil data dari _model_ (database) dan menampilkannya lewat _view_ (halaman).

### `app/Http/Controllers/` (pengatur halaman)

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

### `app/Http/Middleware/` (penjaga yang berjalan di setiap permintaan)

| File                          | Fungsi                                                                                          |
| ----------------------------- | ----------------------------------------------------------------------------------------------- |
| `SetLocale.php`               | Menentukan bahasa tampilan (pilihan pembeli, atau bawaan Indonesia).                            |
| `TrackVisit.php`              | Mencatat kunjungan halaman untuk statistik.                                                     |
| `AutoCancelExpiredOrders.php` | Membatalkan pesanan kedaluwarsa dan membersihkan keranjang lama, tanpa jadwal khusus di server. |
| `SecurityHeaders.php`         | Menambahkan header keamanan di semua halaman.                                                   |
| `cekLogin.php`                | Memastikan anggota tim sudah masuk dan akunnya masih aktif.                                     |
| `cekRole.php`                 | Membatasi halaman sesuai role tim.                                                              |

### `app/Models/` (data di database)

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

### Pendukung di `app/`

| File                                 | Fungsi                                                            |
| ------------------------------------ | ----------------------------------------------------------------- |
| `Services/RajaOngkirService.php`     | Menghubungi RajaOngkir untuk cari tujuan dan hitung ongkir.       |
| `Exceptions/RajaOngkirException.php` | Pesan error yang aman ditampilkan jika layanan ongkir bermasalah. |
| `Exports/OrdersExport.php`           | Susunan file Excel untuk export pesanan.                          |
| `Mail/OrderPlaced.php`               | Email konfirmasi pesanan.                                         |
| `Support/CartSession.php`            | Menandai keranjang milik pengunjung agar tidak hilang saat login. |
| `Support/OrderCleanup.php`           | Aturan pembatalan pesanan kedaluwarsa dan pengembalian stok.      |
| `Support/Img.php`                    | Memilih gambar versi ringan, atau gambar asli jika tidak ada.     |
| `Providers/AppServiceProvider.php`   | Pengaturan awal aplikasi (https dan jumlah isi keranjang).        |

### Folder lainnya

| Lokasi                                           | Fungsi                                                                                                                  |
| ------------------------------------------------ | ----------------------------------------------------------------------------------------------------------------------- |
| `routes/web.php`                                 | Daftar semua alamat halaman dan siapa yang boleh membukanya.                                                            |
| `config/mavnus.php`                              | Pengaturan khas toko: bahasa tersedia, batas waktu pesanan, retensi keranjang, email.                                   |
| `config/services.php`                            | Pengaturan RajaOngkir. File `config/` lain adalah bawaan Laravel.                                                       |
| `database/migrations/`                           | Riwayat pembuatan dan perubahan tabel database.                                                                         |
| `database/seeders/`                              | Pembuat 3 akun tim awal (satu per role).                                                                                |
| `lang/id/`, `lang/en/`                           | Terjemahan per topik: `site`, `auth`, `account`, `checkout`, `order`, `info`, `validation`, `pagination`.               |
| `resources/views/pages/`                         | Halaman utuh: beranda, produk, checkout, akun, login, info, dan halaman dashboard.                                      |
| `resources/views/components/`                    | Potongan tampilan yang dipakai ulang: menu atas, footer, keranjang, filter, tombol bahasa.                              |
| `resources/views/template/`                      | Kerangka halaman: layout pembeli, layout akun, layout dashboard.                                                        |
| `resources/views/emails/`, `exports/`, `errors/` | Email, invoice PDF, dan halaman error (404, 429, 500, 503).                                                             |
| `resources/css/`, `js/`                          | Gaya tampilan (Tailwind) dan skrip pendukung.                                                                           |
| `public/`                                        | Aset yang bisa diakses langsung (logo, banner, `index.php`). Folder `build/` dibuat otomatis saat deploy.               |
| `scripts/`                                       | Pembuat font ikon.                                                                                                      |
| `tests/Feature/`                                 | Tes otomatis: akun, keranjang, checkout, status pesanan, produk, akses tim, tampilan toko.                              |
| `.github/workflows/deploy.yml`                   | Resep auto deploy ke hosting.                                                                                           |
| `.env.example`                                   | Contoh pengaturan untuk komputer sendiri.                                                                               |
| `.env.production.example`                        | Daftar pengaturan untuk server production (tanpa data rahasia), untuk dokumentasi dan pencocokan dengan `.env` hosting. |

### Menjalankan di komputer sendiri

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

> `npm run dev` dan `npm run build` saat ini error karena skrip `scripts/optimize-images.cjs` tidak ada. Pakai `npx vite` dan `npx vite build`. Situs tetap normal dengan gambar asli. Tes otomatis: `php artisan test` (memakai database sementara, data MySQL aman).

## 4. Auto deploy ke production (GitHub Actions + SSH)

### Apa yang terjadi saat push

Setiap `git push` ke branch `main`, GitHub otomatis menyiapkan project dan mengirimnya ke hosting. Tidak perlu upload manual.

**Ini bukan sinkronisasi.** Deploy bekerja dengan cara **menimpa dan menambah**: file yang ada di GitHub dikirim ulang dan menimpa file lama yang namanya sama, file baru ditambahkan. **File yang dihapus dari GitHub tidak ikut terhapus di server**, jadi harus dihapus manual.

Urutannya:

1. GitHub menyiapkan project: memasang library dan membuat CSS, JS, dan ikon.
2. Mengirim file aplikasi ke folder aplikasi di server (`APP_PATH`).
3. Mengirim file tampilan publik ke folder publik domain (`PUBLIC_PATH`).
4. Menjalankan perintah di server: membersihkan cache lama, memperbarui struktur database (migrasi), menyambungkan folder foto, dan menyimpan ulang pengaturan.
5. Menghapus kunci SSH sementara.

File dikirim dengan `tar` karena server tidak punya `rsync`.

### Yang ikut terkirim, dan yang tidak

| Terkirim dan ditimpa setiap push                                                                                                                                                      | Tidak disentuh deploy                                                                                                                                                                |
| ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| Kode aplikasi (`app`, `config`, `routes`, `lang`, `resources`, `database`), library (`vendor`), hasil build tampilan (`public/build`), logo dan banner (`public/aset`), `favicon.ico` | `.env`, folder `storage/` (foto produk, log, sesi), `public/index.php`, `public/.htaccess`, **isi database** (hanya struktur yang diperbarui lewat migrasi; seeder tidak ikut jalan) |

Tidak pernah dikirim: `.git`, `.github`, `tests`, `node_modules`.

### Yang harus dilakukan manual

| Hal                                           | Penjelasan                                                                                                                                                                                                                                                            |
| --------------------------------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Mengubah `.env` di server**                 | Server menyimpan salinan pengaturan setiap deploy, jadi perubahan `.env` baru terbaca setelah deploy berikutnya. Setelah mengedit `.env`, jalankan ulang lewat tab **Actions → Run workflow**. Daftar lengkap isi `.env` production ada di `.env.production.example`. |
| **Menghapus file di server**                  | File yang dihapus dari GitHub (misalnya `robots.txt` lama) tetap ada di server. Hapus lewat cPanel File Manager.                                                                                                                                                      |
| **`public/index.php` dan `.htaccess` server** | Disesuaikan sekali saat setup dan tidak ditimpa deploy.                                                                                                                                                                                                               |
| **Password akun tim**                         | Akun dibuat dari seeder dengan password awal yang tertulis di kode. Ganti lewat database (phpMyAdmin) sebelum dipakai sungguhan.                                                                                                                                      |
| **Backup database**                           | Lakukan lewat phpMyAdmin sebelum push yang mengubah struktur tabel.                                                                                                                                                                                                   |
| **Versi PHP di cPanel**                       | Harus 8.4.1 atau lebih baru (MultiPHP Manager).                                                                                                                                                                                                                       |
| **Pindah ke domain utama**                    | Ubah secret `APP_PATH` dan `PUBLIC_PATH` ke folder domain utama (atau buat workflow kedua), lalu sesuaikan `.env` (terutama `APP_URL`).                                                                                                                               |

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

5. **Cek server:** backup `APP_PATH` dan `PUBLIC_PATH`, pastikan PHP 8.4.1+, dan pastikan `index.php` di `PUBLIC_PATH` menunjuk ke `APP_PATH`.
6. **Pasang workflow** `.github/workflows/deploy.yml`, commit, lalu push ke `main`. Hasilnya terlihat di tab **Actions**, dan bisa dijalankan manual lewat **Run workflow**.

Untuk project Laravel lain: salin `deploy.yml`, sesuaikan versi PHP dan Node, perintah build, branch, dan daftar `--exclude`, lalu buat SSH key dan secrets baru untuk tiap hosting.

### Kalau gagal

| Pesan error                                                    | Penyebab dan solusi                                                             |
| -------------------------------------------------------------- | ------------------------------------------------------------------------------- |
| `Your lock file does not contain a compatible set of packages` | Versi PHP di workflow terlalu rendah. Naikkan `php-version` (minimal 8.4).      |
| `Permission denied (publickey)`                                | Key belum di-**Authorize** di cPanel, atau isi `SSH_PRIVATE_KEY` tidak lengkap. |
| `Connection timed out`                                         | `SSH_PORT` salah, atau hosting memblokir koneksi dari GitHub.                   |
| Situs error 500 padahal deploy hijau                           | Versi PHP domain di cPanel turun, atau `.env` server belum punya variabel baru. |

### Yang perlu diperhatikan

- **Push ke `main` = langsung naik ke server.** Tidak ada tes dan tidak ada rollback otomatis. Jika bermasalah, `git revert` lalu push lagi.
- **Migrasi database jalan otomatis** di setiap deploy. Aman untuk tabel atau kolom baru, berisiko untuk menghapus atau mengganti nama kolom. Jangan edit migrasi lama, buat migrasi baru.
- **Jangan edit kode langsung di server.** Akan tertimpa deploy berikutnya.
- **Jangan commit atau kirim private key lewat chat.** Jika bocor, hapus key di cPanel dan buat yang baru.
- **Upload tidak atomik:** selama sekitar 40 detik situs bisa sebentar setengah terupdate.
- **Runner GitHub:** `ubuntu-latest` mulai bertahap pindah ke Ubuntu 26.04 antara 19 Oktober dan 19 November 2026. Agar aman, ganti `runs-on` di workflow menjadi `ubuntu-24.04`.

## 5. Kesimpulan saat ini

- **Sudah berjalan:** toko lengkap dari katalog, keranjang, checkout dengan ongkir otomatis, akun pembeli, sampai dashboard tim dengan tiga role, invoice, export, dan statistik. Situs tersedia dalam dua bahasa (Indonesia dan Inggris).
- **Sudah dirapikan baru-baru ini:** pengaman koneksi ke layanan ongkir diaktifkan, pencatatan kunjungan tidak lagi bisa membuat halaman error, sisa fitur Google dibersihkan, dan pesan form lengkap di dua bahasa.
- **Belum ada:** pembayaran online (menunggu Midtrans), login Google (ditunda), lupa password, dan halaman kelola akun tim.
- **Sebelum pindah ke domain utama:**
    1. Cocokkan `.env` hosting dengan `.env.production.example` (terutama `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` https, `SESSION_SECURE_COOKIE=true`).
    2. Hapus `robots.txt` lama di server.
    3. Ganti password akun tim bawaan seeder.
    4. Siapkan `APP_PATH` dan `PUBLIC_PATH` untuk domain utama.
    5. Jalankan ulang workflow setelah `.env` diubah, lalu uji checkout dan ongkir di alamat baru.
    6. Putuskan cara menangani pesanan selama pembayaran online belum aktif (pesanan kedaluwarsa dalam 60 menit).
