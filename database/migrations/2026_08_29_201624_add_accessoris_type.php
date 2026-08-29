<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Accessories sementara cuma ada 3 jenis: keychain, sticker, totebag.
        // Gak pakai product_variants karena accessories gak butuh pilihan
        // ukuran/varian — stoknya tunggal, ada di kolom products.stock.
        Schema::table('accessories', function (Blueprint $table) {
            $table->enum('type', ['keychain', 'sticker', 'totebag'])->after('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accessories', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};