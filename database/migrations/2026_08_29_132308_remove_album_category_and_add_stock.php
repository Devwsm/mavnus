<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Kategori 'album' gak pernah punya controller buat nambah produk dari
        // aplikasi, jadi baris ini cuma jaga-jaga kalau ada data manual/seed lama.
        // FK cascadeOnDelete di albums/images/variants/cart_items bakal ikut kehapus.
        DB::table('products')->where('category', 'album')->delete();

        Schema::dropIfExists('albums');

        // Hilangin 'album' dari daftar kategori yang valid di kolom enum.
        DB::statement("ALTER TABLE products MODIFY category ENUM('clothes', 'accessories') NOT NULL");

        // Stok tunggal buat kategori yang gak butuh variant (accessories, dst).
        // Clothes tetap pakai product_variants seperti biasa — kolom ini dibiarkan
        // NULL untuk produk clothes.
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('stock')->nullable()->after('weight');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('stock');
        });

        DB::statement("ALTER TABLE products MODIFY category ENUM('clothes', 'accessories', 'album') NOT NULL");

        Schema::create('albums', function (Blueprint $table) {
            $table->id('id_album');
            $table->foreignId('product_id')->constrained('products', 'id_product')->cascadeOnDelete();
            $table->timestamps();
        });
    }
};