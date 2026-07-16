<?php

namespace App\Http\Controllers;

use App\Exports\OrdersExport;
use App\Exports\ProductsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class importExportController extends Controller
{
    /**
     * Halaman utama Import/Export di dashboard.
     */
    public function index()
    {
        return view('pages.dashboard.import-export');
    }

    // ================= Export Excel (laporan, buat staff) =================

    /**
     * Export daftar produk (clothes) ke Excel — untuk dilihat/dibagikan sebagai laporan,
     * bukan untuk migrasi data.
     */
    public function exportProducts()
    {
        try {
            $filename = 'mavnus-produk-' . now()->format('Y-m-d_His') . '.xlsx';
            return Excel::download(new ProductsExport, $filename);
        } catch (\Throwable $e) {
            return redirect()
                ->route('dashboard.import-export')
                ->with('error', 'Gagal export produk: ' . $e->getMessage());
        }
    }

    /**
     * Export daftar order + rincian barangnya ke Excel — untuk laporan penjualan.
     */
    public function exportOrders()
    {
        try {
            $filename = 'mavnus-order-' . now()->format('Y-m-d_His') . '.xlsx';
            return Excel::download(new OrdersExport, $filename);
        } catch (\Throwable $e) {
            return redirect()
                ->route('dashboard.import-export')
                ->with('error', 'Gagal export order: ' . $e->getMessage());
        }
    }

    /**
     * Export data produk (products, clothes, accessories, albums, product_variants,
     * product_images) sebagai SQL — hanya INSERT, tanpa DROP/CREATE TABLE.
     * Dipakai setelah migrate:fresh + db:seed di local, supaya data real production
     * bisa dimasukkan ke struktur tabel yang sudah bersih tanpa konflik.
     */
    public function exportProductsSql()
    {
        try {
            $tables = ['products', 'clothes', 'accessories', 'albums', 'product_variants', 'product_images'];
            $sql = $this->generateInsertOnlySql($tables, 'Produk');
            $filename = 'mavnus-produk-' . now()->format('Y-m-d_His') . '.sql';
            return response($sql)
                ->header('Content-Type', 'application/sql')
                ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
        } catch (\Throwable $e) {
            return redirect()
                ->route('dashboard.import-export')
                ->with('error', 'Gagal export SQL produk: ' . $e->getMessage());
        }
    }

    /**
     * Export data order (orders, order_items) sebagai SQL — hanya INSERT.
     */
    public function exportOrdersSql()
    {
        try {
            $tables = ['orders', 'order_items'];
            $sql = $this->generateInsertOnlySql($tables, 'Order');
            $filename = 'mavnus-order-' . now()->format('Y-m-d_His') . '.sql';
            return response($sql)
                ->header('Content-Type', 'application/sql')
                ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
        } catch (\Throwable $e) {
            return redirect()
                ->route('dashboard.import-export')
                ->with('error', 'Gagal export SQL order: ' . $e->getMessage());
        }
    }

    /**
     * Helper: generate SQL berisi INSERT statement saja (tanpa struktur tabel)
     * untuk daftar tabel yang diberikan, sesuai urutan yang dikasih (penting untuk
     * menjaga foreign key, misal 'products' harus diinsert sebelum 'clothes').
     */
    private function generateInsertOnlySql(array $tables, string $label): string
    {
        $sql = "-- Mavnus {$label} Data Export\n-- Generated: " . now() . "\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $tableName) {
            $rows = DB::table($tableName)->get();
            if ($rows->isEmpty()) {
                $sql .= "-- Tabel `{$tableName}` kosong, tidak ada data.\n\n";
                continue;
            }

            foreach ($rows as $row) {
                $rowArray = (array) $row;
                $columns = array_map(fn($col) => "`{$col}`", array_keys($rowArray));
                $values = array_map(function ($value) {
                    if (is_null($value)) {
                        return 'NULL';
                    }
                    return DB::getPdo()->quote($value);
                }, array_values($rowArray));
                $sql .= "INSERT INTO `{$tableName}` (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ");\n";
            }
            $sql .= "\n";
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
        return $sql;
    }

    // ================= Export Database & Storage (buat migrasi/backup) =================

    /**
     * Export seluruh isi database jadi file .sql (struktur tabel + data).
     * Dipakai sebelum update besar / pindah project, bukan untuk pemakaian harian staff.
     * Ditulis manual pakai query (bukan shell mysqldump) karena shared hosting
     * biasanya mematikan shell_exec() demi keamanan.
     */
    public function exportDatabase()
    {
        try {
            $tables = DB::select('SHOW TABLES');
            $sql = "-- Mavnus Database Backup\n-- Generated: " . now() . "\n\n";
            $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

            foreach ($tables as $table) {
                // Ambil nama tabel secara dinamis, karena nama key dari SHOW TABLES
                // bisa beda case tergantung OS/server (mis. Tables_in_mavnus_db vs _DB)
                $tableName = array_values((array) $table)[0];

                // Simpan struktur tabel (CREATE TABLE)
                $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`")[0]->{'Create Table'};
                $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                $sql .= $createTable . ";\n\n";

                // Simpan seluruh isi data tabel sebagai INSERT statement
                $rows = DB::table($tableName)->get();
                foreach ($rows as $row) {
                    $rowArray = (array) $row;
                    $columns = array_map(fn($col) => "`{$col}`", array_keys($rowArray));
                    $values = array_map(function ($value) {
                        if (is_null($value)) {
                            return 'NULL';
                        }
                        return DB::getPdo()->quote($value);
                    }, array_values($rowArray));
                    $sql .= "INSERT INTO `{$tableName}` (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ");\n";
                }
                $sql .= "\n";
            }
            $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

            $filename = 'mavnus-database-' . now()->format('Y-m-d_His') . '.sql';

            return response($sql)
                ->header('Content-Type', 'application/sql')
                ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
        } catch (\Throwable $e) {
            return redirect()
                ->route('dashboard.import-export')
                ->with('error', 'Gagal export database: ' . $e->getMessage());
        }
    }

    /**
     * Export seluruh isi folder storage/app/public (foto produk) jadi satu file .zip.
     * Wajib didownload berpasangan dengan exportDatabase(), karena SQL dump
     * tidak membawa file gambar — hanya path/referensinya saja.
     */
    public function exportStorage()
    {
        try {
            $zipFileName = 'mavnus-storage-' . now()->format('Y-m-d_His') . '.zip';
            $zipPath = storage_path('app/' . $zipFileName);

            $zip = new \ZipArchive();
            if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
                throw new \Exception('Tidak bisa membuat file zip.');
            }

            $sourcePath = storage_path('app/public');
            if (!is_dir($sourcePath)) {
                throw new \Exception('Folder storage tidak ditemukan.');
            }

            // Iterasi rekursif ke semua file di dalam storage/app/public, termasuk subfolder
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($sourcePath),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($sourcePath) + 1);
                    $zip->addFile($filePath, $relativePath);
                }
            }
            $zip->close();

            // File zip dihapus dari server otomatis setelah selesai didownload,
            // biar tidak numpuk jadi sampah di storage server
            return response()->download($zipPath)->deleteFileAfterSend(true);
        } catch (\Throwable $e) {
            return redirect()
                ->route('dashboard.import-export')
                ->with('error', 'Gagal export foto: ' . $e->getMessage());
        }
    }
}