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
        Schema::create('clothes_variants', function (Blueprint $table) {
            $table->id('id_clothes_variant');
            $table->foreignId('clothes_id')->constrained('clothes', 'id_clothes')->cascadeOnDelete();
            $table->enum('size', ['S', 'M', 'L', 'XL']);
            $table->unsignedInteger('stock')->default(0);
            $table->timestamps();

            $table->unique(['clothes_id', 'size']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};