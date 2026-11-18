<?php

namespace App\Http\Controllers;

use App\Exports\OrdersExport;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class importExportController extends Controller
{
    /**
     * Tabel yang isinya tidak ikut backup karena cuma data sementara. Terutama `sessions`
     * (ID & payload sesi login staf/customer) yang tidak boleh tersebar lewat file backup.
     * Strukturnya tetap ikut di-backup, datanya saja yang dilewati.
     */
    private const SKIP_DATA_TABLES = ['sessions', 'cache', 'cache_locks', 'jobs', 'job_batches', 'failed_jobs', 'password_reset_tokens'];

    /**
     * Halaman utama Import/Export di dashboard.
     */
    public function index()
    {
        return view('pages.dashboard.import-export');
    }

    // ================= Invoice & laporan pesanan =================

    /**
     * Download data order sebagai Excel — format tabular sederhana untuk diolah,
     * bukan tampilan invoice.
     */
    public function exportOrders(Request $request)
    {
        return $this->safely('Gagal export data order.', function () use ($request) {
            $export = new OrdersExport($this->orderFilters($request));

            if ($export->collection()->isEmpty()) {
                return $this->noOrdersFound();
            }

            return Excel::download($export, 'mavnus-order-' . $this->timestamp() . '.xlsx');
        });
    }

    /**
     * Preview invoice semua order dalam bentuk HTML, dibuka di tab baru
     * sebelum staff memutuskan untuk download versi PDF/Excel-nya.
     */
    public function ordersInvoicePreview(Request $request)
    {
        $orders = $this->filteredOrders($request);

        return $orders->isEmpty() ? $this->noOrdersFound() : view('exports.orders-invoice', compact('orders'));
    }

    /**
     * Download invoice semua order sebagai PDF — hasilnya identik dengan preview,
     * karena sama-sama merender HTML/CSS yang sama.
     */
    public function exportOrdersPdf(Request $request)
    {
        return $this->safely('Gagal export invoice PDF.', function () use ($request) {
            $orders = $this->filteredOrders($request);

            return $orders->isEmpty()
                ? $this->noOrdersFound()
                : $this->invoicePdf($orders)->download('mavnus-invoice-order-' . $this->timestamp() . '.pdf');
        });
    }

    /**
     * Preview invoice SATU pesanan (dibuka dari halaman detail pesanan).
     */
    public function orderInvoicePreview(Order $order)
    {
        $orders = collect([$order->load('items')]);

        return view('exports.orders-invoice', compact('orders'));
    }

    /**
     * Download invoice SATU pesanan sebagai PDF (tampilan sama dengan preview).
     */
    public function orderInvoicePdf(Order $order)
    {
        return $this->safely('Gagal membuat invoice PDF.', function () use ($order) {
            return $this->invoicePdf(collect([$order->load('items')]))
                ->download('mavnus-invoice-' . $order->order_number . '.pdf');
        }, route('dashboard.orders.show', $order));
    }

    /** Filter yang dipakai export pesanan: status, from, to (tanggal). Pencarian teks hanya untuk daftar pesanan. */
    private function orderFilters(Request $request): array
    {
        return $request->only(['status', 'from', 'to']);
    }

    private function filteredOrders(Request $request): Collection
    {
        return Order::with('items')->filter($this->orderFilters($request))->latest()->get();
    }

    private function invoicePdf(Collection $orders)
    {
        return Pdf::loadView('exports.orders-invoice', compact('orders'))->setPaper('a4', 'portrait');
    }

    private function noOrdersFound()
    {
        return redirect()
            ->route('dashboard.import-export')
            ->with('error', 'Tidak ada pesanan pada rentang tanggal / status yang dipilih.');
    }

    // ================= Export data (SQL) =================

    /**
     * Export data produk (products, clothes, accessories, product_variants,
     * product_images) sebagai SQL — hanya INSERT, tanpa DROP/CREATE TABLE.
     * Dipakai setelah migrate:fresh + db:seed di local, supaya data real production
     * bisa dimasukkan ke struktur tabel yang sudah bersih tanpa konflik.
     */
    public function exportProductsSql()
    {
        return $this->safely('Gagal export SQL produk.', fn() => $this->sqlDownload(
            $this->insertOnlySql(['products', 'clothes', 'accessories', 'product_variants', 'product_images'], 'Produk'),
            'mavnus-produk'
        ));
    }

    /**
     * Export data order (orders, order_items) sebagai SQL — hanya INSERT.
     */
    public function exportOrdersSql()
    {
        return $this->safely('Gagal export SQL order.', fn() => $this->sqlDownload(
            $this->insertOnlySql(['orders', 'order_items'], 'Order'),
            'mavnus-order'
        ));
    }

    /**
     * Export seluruh isi database jadi file .sql (struktur tabel + data).
     * Dipakai sebelum update besar / pindah project, bukan untuk pemakaian harian staff.
     * Ditulis manual pakai query (bukan shell mysqldump) karena shared hosting
     * biasanya mematikan shell_exec() demi keamanan.
     */
    public function exportDatabase()
    {
        return $this->safely('Gagal export database.', function () {
            $sql = $this->sqlHeader('Database Backup') . "SET FOREIGN_KEY_CHECKS=0;\n\n";

            foreach (DB::select('SHOW TABLES') as $table) {
                // Ambil nama tabel secara dinamis, karena nama key dari SHOW TABLES
                // bisa beda case tergantung OS/server (mis. Tables_in_mavnus_db vs _DB)
                $tableName = array_values((array) $table)[0];

                $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                $sql .= DB::select("SHOW CREATE TABLE `{$tableName}`")[0]->{'Create Table'} . ";\n\n";

                if (! in_array($tableName, self::SKIP_DATA_TABLES, true)) {
                    $sql .= $this->insertStatements($tableName) . "\n";
                }
            }

            return $this->sqlDownload($sql . "SET FOREIGN_KEY_CHECKS=1;\n", 'mavnus-database');
        });
    }

    /**
     * Export seluruh isi folder storage/app/public (foto produk) jadi satu file .zip.
     * Wajib didownload berpasangan dengan exportDatabase(), karena SQL dump
     * tidak membawa file gambar — hanya path/referensinya saja.
     */
    public function exportStorage()
    {
        return $this->safely('Gagal export foto.', function () {
            $sourcePath = storage_path('app/public');
            if (! is_dir($sourcePath)) {
                throw new \Exception('Folder storage tidak ditemukan.');
            }

            $zipPath = storage_path('app/mavnus-storage-' . $this->timestamp() . '.zip');

            $zip = new \ZipArchive();
            if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
                throw new \Exception('Tidak bisa membuat file zip.');
            }

            // Iterasi rekursif ke semua file di dalam storage/app/public, termasuk subfolder
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($sourcePath),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $file) {
                if (! $file->isDir()) {
                    $filePath = $file->getRealPath();
                    $zip->addFile($filePath, substr($filePath, strlen($sourcePath) + 1));
                }
            }
            $zip->close();

            // File zip dihapus dari server otomatis setelah selesai didownload,
            // biar tidak numpuk jadi sampah di storage server
            return response()->download($zipPath)->deleteFileAfterSend(true);
        });
    }

    // ================= Helper =================

    /**
     * Jalankan sebuah export; kalau gagal, catat di log dan kembalikan staf ke halaman
     * dengan pesan error (bukan halaman 500).
     */
    private function safely(string $failMessage, callable $export, ?string $redirectTo = null)
    {
        try {
            return $export();
        } catch (\Throwable $e) {
            report($e);

            return redirect($redirectTo ?? route('dashboard.import-export'))
                ->with('error', $failMessage . ' Detail error tercatat di log server.');
        }
    }

    private function timestamp(): string
    {
        return now()->format('Y-m-d_His');
    }

    private function sqlHeader(string $title): string
    {
        return "-- Mavnus {$title}\n-- Generated: " . now() . "\n\n";
    }

    private function sqlDownload(string $sql, string $filenamePrefix)
    {
        return response($sql)
            ->header('Content-Type', 'application/sql')
            ->header('Content-Disposition', "attachment; filename=\"{$filenamePrefix}-" . $this->timestamp() . ".sql\"");
    }

    /**
     * SQL berisi INSERT statement saja (tanpa struktur tabel) untuk daftar tabel yang diberikan,
     * sesuai urutan yang dikasih (penting untuk menjaga foreign key, misal 'products'
     * harus diinsert sebelum 'clothes').
     */
    private function insertOnlySql(array $tables, string $label): string
    {
        $sql = $this->sqlHeader("{$label} Data Export") . "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $tableName) {
            $statements = $this->insertStatements($tableName);
            $sql .= ($statements === '' ? "-- Tabel `{$tableName}` kosong, tidak ada data.\n" : $statements) . "\n";
        }

        return $sql . "SET FOREIGN_KEY_CHECKS=1;\n";
    }

    /** Satu baris INSERT per baris data di tabel (string kosong kalau tabelnya kosong). */
    private function insertStatements(string $tableName): string
    {
        $pdo = DB::getPdo();
        $sql = '';

        foreach (DB::table($tableName)->get() as $row) {
            $row = (array) $row;
            $columns = implode(', ', array_map(fn($column) => "`{$column}`", array_keys($row)));
            $values = implode(', ', array_map(fn($value) => is_null($value) ? 'NULL' : $pdo->quote($value), array_values($row)));

            $sql .= "INSERT INTO `{$tableName}` ({$columns}) VALUES ({$values});\n";
        }

        return $sql;
    }
}