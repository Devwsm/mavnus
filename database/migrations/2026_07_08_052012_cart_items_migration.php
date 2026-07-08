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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id('id_cart_item');
            $table->string('session_id');
            $table->foreignId('product_id')->constrained('products', 'id_product')->cascadeOnDelete();
            $table->foreignId('clothes_variant_id')->nullable()
                ->constrained('clothes_variants', 'id_clothes_variant')->nullOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('cart_items');
    }
};