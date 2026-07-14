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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id('id_variant');
            $table->foreignId('product_id')->constrained('products', 'id_product')->cascadeOnDelete();
            $table->string('label'); // "S"/"M"/"L"/"XL" untuk clothes, "CD"/"Vinyl" untuk album, dst — bebas per kategori
            $table->unsignedInteger('stock')->default(0);
            $table->timestamps();

            $table->unique(['product_id', 'label']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('product_variants');
    }
};