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
        //
        Schema::create('order_items', function (Blueprint $table) {
            $table->id('id_order_item');
            $table->foreignId('order_id')->constrained('orders', 'id_order')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products', 'id_product');
            $table->foreignId('variant_id')->nullable()->constrained('product_variants', 'id_variant');

            // Snapshot data produk saat order dibuat — biar riwayat order gak berubah
            // walau staff nanti edit nama/harga produk aslinya
            $table->string('product_name');
            $table->string('variant_label')->nullable();
            $table->unsignedBigInteger('price');
            $table->unsignedInteger('quantity');
            $table->unsignedBigInteger('subtotal');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('order_items');
    }
};