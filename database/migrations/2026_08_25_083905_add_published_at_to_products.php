<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // null = belum dijadwalkan sama sekali (dianggap belum publish)
            // <= now()  -> udah tayang di storefront
            // > now()   -> masih terjadwal, belum kelihatan di publik
            $table->timestamp('published_at')->nullable()->after('is_active');
        });

        // Produk yang udah ada sebelum fitur ini dianggap "publish sekarang"
        // biar gak tiba-tiba ilang dari storefront pas migration ini jalan.
        DB::table('products')->whereNull('published_at')->update([
            'published_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('published_at');
        });
    }
};