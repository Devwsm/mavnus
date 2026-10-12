# Mavnus

Toko online merchandise resmi Whisnu Santika. Pembeli bisa belanja tanpa wajib punya akun, dan tim punya dashboard sendiri untuk mengelola produk dan pesanan.

**Status:** sudah berjalan di server dan dipakai tim untuk preview lewat subdomain (nanti dipindah ke domain utama). Pembayaran online belum aktif, lihat bagian [Belum tersedia](#belum-tersedia).

## Fitur

### Untuk pembeli

- Dua kategori produk: **Clothes** (ukuran S/M/L/XL) dan **Accessories** (keychain, sticker, totebag).
- Cari produk (muncul saran saat mengetik) dan filter berdasarkan harga.
- Keranjang belanja tanpa perlu login.
- Checkout dengan ongkos kirim otomatis lewat RajaOngkir. Ongkir dan total dihitung ulang oleh server, jadi tidak bisa diubah dari browser. Stok langsung dikurangi, sehingga tidak bisa terjual melebihi stok.
- Akun pembeli (opsional): lihat riwayat dan status pesanan, ubah profil, hapus akun. Jika akun dihapus, pesanan tetap tersimpan untuk rekap toko tetapi tidak lagi terhubung ke akun.
- Halaman "pesanan berhasil" hanya bisa dibuka oleh pembeli yang membuat pesanan itu.
- Produk bisa dijadwalkan rilis, dan otomatis ditandai habis saat stok kosong.
- Pesanan yang masih "menunggu" dibatalkan dan dihapus otomatis setelah 60 menit, stoknya dikembalikan. Tidak butuh pengaturan jadwal di server.
- Berlangganan newsletter lewat footer (email tersimpan di database).
- Halaman info/bantuan dan sitemap untuk mesin pencari.

### Untuk tim (dashboard)

Login tim ada di `/crew-portal` dan terpisah dari login pembeli.

| Role              | Yang bisa dilakukan                                                          |
| ----------------- | ---------------------------------------------------------------------------- |
| **Owner**         | Semuanya, plus ringkasan omzet, statistik pengunjung, dan backup data.       |
| **Admin Produk**  | Tambah, ubah, hapus produk; atur foto, ukuran, dan stok; export data produk. |
| **Staff Pesanan** | Lihat pesanan dan ubah statusnya; export pesanan dan invoice.                |

- **Status pesanan:** menunggu → diproses → dikirim → selesai. Pesanan bisa dibatalkan sebelum dikirim (stok dikembalikan). Status bisa dikoreksi mundur jika salah klik, tetapi pesanan yang dibatalkan terkunci.
- **Foto produk:** maksimal 8 foto per produk, 5 MB per foto. Otomatis dikecilkan dan diubah ke format WebP agar halaman ringan.
- **Ringkasan owner:** omzet 6 bulan terakhir, omzet per kategori bulan ini, dan produk terlaris. Omzet hanya dihitung dari pesanan yang sudah selesai.
- **Statistik pengunjung:** perangkat, browser, dan halaman terpopuler, tanpa layanan analitik luar. Sistem mencatat alamat IP pengunjung, jadi sesuaikan kebijakan privasi bila perlu.
- **Export:** pesanan (Excel), rekap invoice semua pesanan (PDF), data produk dan pesanan (SQL). Khusus Owner: backup database dan backup foto (zip).
- **Akun tim** bisa dinonaktifkan lewat database, tetapi belum ada halaman untuk mengelolanya. Akun awal dibuat oleh seeder (satu per role).

### Keamanan

Percobaan login dibatasi, endpoint publik (keranjang, pencarian, checkout, newsletter) dibatasi jumlah permintaannya, dan semua halaman dikirim dengan header keamanan standar.

## Belum tersedia

- **Pembayaran online (Midtrans).** Menunggu persetujuan. Kolom di database sudah disiapkan, tetapi belum ada halaman pembayaran: setelah checkout, pesanan tercatat "belum dibayar".
  **Penting:** selama pembayaran belum aktif, pesanan berstatus "menunggu" akan terhapus otomatis setelah 60 menit. Staff harus memproses pesanan sebelum itu, atau perpanjang waktunya lewat `ORDER_EXPIRE_MINUTES`.
- **Login dengan Google.** Ditunda karena rumit dan berisiko untuk akun pembeli. Hanya tersisa pengecekan kecil di halaman akun yang belum berpengaruh apa pun.

## Teknologi

Laravel 13 (PHP **8.4.1 atau lebih baru**), MySQL, Blade, Tailwind CSS v4, dan Vite. Pustaka utama: DomPDF (invoice PDF), Laravel Excel (export Excel), Intervention Image (foto WebP), SweetAlert2 (dialog konfirmasi). Layanan luar: RajaOngkir (Komerce) untuk ongkir.

## Menjalankan di komputer sendiri

Butuh PHP 8.4+, Composer, Node.js 22, dan MySQL.

```bash
composer install
npm install
cp .env.example .env        # Windows: copy .env.example .env
php artisan key:generate
```

Isi `.env` (database dan kunci RajaOngkir, lihat tabel di bawah), lalu:

```bash
php artisan migrate --seed  # buat tabel + 3 akun tim contoh
npm run icons               # buat font ikon
npx vite                    # jalankan tampilan (terminal 1)
php artisan serve           # jalankan situs (terminal 2)
```

> **Catatan:** `npm run dev` dan `npm run build` saat ini error karena skrip `scripts/optimize-images.cjs` tidak ada di repo. Gunakan `npx vite` dan `npx vite build` seperti di atas. Tanpa skrip itu situs tetap normal, hanya memakai gambar asli.

**Pengaturan `.env` tambahan** (belum ada di `.env.example`):

| Variabel                         | Fungsi                                                                      |
| -------------------------------- | --------------------------------------------------------------------------- |
| `RAJAONGKIR_API_KEY`             | Wajib. Kunci API untuk hitung ongkir.                                       |
| `RAJAONGKIR_BASE_URL`            | Wajib. Contoh: `https://rajaongkir.komerce.id/api/v1`.                      |
| `RAJAONGKIR_ORIGIN_ID`           | Wajib. ID lokasi asal pengiriman toko.                                      |
| `ORDER_EXPIRE_MINUTES`           | Opsional, default 60. Batas waktu pesanan "menunggu" sebelum dibatalkan.    |
| `ORDER_CLEANUP_THROTTLE_MINUTES` | Opsional, default 5. Jeda minimal antar pemeriksaan pesanan kedaluwarsa.    |
| `CART_RETENTION_DAYS`            | Opsional, default 30. Keranjang yang ditinggalkan dihapus setelah hari ini. |

**Tes otomatis:** jalankan `php artisan test`. Tes memakai database sementara, jadi data MySQL kamu aman.

**Sebelum production:** ganti password semua akun tim (password awal seeder ada di kode, jadi dianggap tidak aman), set `APP_ENV=production` dan `APP_DEBUG=false`.

## Auto deploy ke hosting cPanel (GitHub Actions + SSH)

Setiap `git push` ke branch `main`, GitHub otomatis menyiapkan project lalu mengirimnya ke hosting lewat SSH. Tidak perlu upload manual. Panduan ini bisa dipakai ulang untuk project Laravel lain di cPanel.

**Alurnya:** GitHub memasang dependensi dan membuat tampilan (Vite) → mengirim file ke server → menjalankan perintah Laravel di server (migrasi database dan cache). File dikirim dengan `tar` karena server tidak punya `rsync`.

**Tidak ditimpa di server:** `.env`, folder `storage/`, `public/index.php`, dan `public/.htaccess`. File yang dihapus dari repo **tidak ikut terhapus** di server.

### Setup (sekali per hosting)

1. **Buat SSH key** di laptop (di luar folder project). PowerShell:
    ```powershell
    ssh-keygen -t ed25519 -f "$HOME\.ssh\nama_project_deploy" -N '""'
    ```
2. **Pasang public key di cPanel:** SSH Access → Manage SSH Keys → Import Key (tempel public key), lalu **Authorize**.
3. **Catat port SSH.** Sering bukan 22, cek di panduan hosting.
4. **Isi GitHub secrets** (repo → Settings → Secrets and variables → Actions). Setelah tersimpan, hapus file private key dari laptop.

    | Secret            | Isi                                                        |
    | ----------------- | ---------------------------------------------------------- |
    | `SSH_PRIVATE_KEY` | Isi private key lengkap (termasuk baris `BEGIN` dan `END`) |
    | `SSH_HOST`        | Alamat server cPanel                                       |
    | `SSH_PORT`        | Port SSH                                                   |
    | `SSH_USER`        | Username cPanel                                            |
    | `APP_PATH`        | Folder aplikasi Laravel di server                          |
    | `PUBLIC_PATH`     | Folder publik (document root) domain                       |

5. **Cek server:** backup `APP_PATH` dan `PUBLIC_PATH`, pastikan versi PHP di server **8.4.1+**, dan pastikan `index.php` di `PUBLIC_PATH` menunjuk ke `APP_PATH`.
6. **Pasang workflow:** file `.github/workflows/deploy.yml`, commit, lalu push ke `main`. Hasilnya terlihat di tab **Actions**. Bisa juga dijalankan manual lewat tombol **Run workflow**.

### Dipakai di project lain

Salin `deploy.yml`, lalu sesuaikan versi PHP dan Node, perintah build, branch pemicu, dan daftar `--exclude`. Buat SSH key dan secrets baru untuk setiap hosting. Untuk preview dan production yang terpisah, pakai dua workflow dengan branch dan secrets berbeda.

### Kalau gagal

| Pesan error                                                    | Penyebab dan solusi                                                             |
| -------------------------------------------------------------- | ------------------------------------------------------------------------------- |
| `Your lock file does not contain a compatible set of packages` | Versi PHP di workflow terlalu rendah. Naikkan `php-version` (minimal 8.4).      |
| `Permission denied (publickey)`                                | Key belum di-**Authorize** di cPanel, atau isi `SSH_PRIVATE_KEY` tidak lengkap. |
| `Connection timed out`                                         | `SSH_PORT` salah, atau hosting memblokir koneksi dari GitHub.                   |
| `php: command not found` / PHP server terlalu rendah           | Ganti versi PHP lewat MultiPHP Manager di cPanel.                               |
| Situs error 500 padahal deploy hijau                           | Versi PHP domain di cPanel turun, atau `.env` server belum punya variabel baru. |

### Yang perlu diperhatikan

- **Push ke `main` = langsung naik ke server.** Tidak ada tes dan tidak ada rollback otomatis. Jika bermasalah, `git revert` lalu push lagi.
- **Migrasi database jalan otomatis** di setiap deploy. Aman untuk tabel atau kolom baru. Berisiko untuk menghapus atau mengganti nama kolom/tabel. Backup database (phpMyAdmin) sebelum push yang mengubah struktur, dan jangan edit migrasi lama, buat migrasi baru. Seeder tidak ikut jalan.
- **Perubahan `.env` harus diedit manual di server.** Variabel baru yang belum ditambahkan bisa membuat situs error.
- **Jangan edit kode langsung di server.** Akan tertimpa deploy berikutnya.
- **Jangan commit atau kirim private key lewat chat.** Jika bocor, hapus key di cPanel dan buat yang baru.
- **Upload tidak atomik:** selama sekitar 40 detik situs bisa sebentar setengah terupdate.
- **Runner GitHub:** `ubuntu-latest` mulai bertahap pindah ke Ubuntu 26.04 antara 19 Oktober dan 19 November 2026. Jika ingin aman, ganti `runs-on` di workflow menjadi `ubuntu-24.04`.
