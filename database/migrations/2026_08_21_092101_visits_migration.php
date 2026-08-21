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
        Schema::create('visits', function (Blueprint $table) {
            $table->id();

            $table->string('ip_address', 45)->nullable();
            $table->string('session_id')->nullable();
            $table->string('url');
            $table->string('referrer')->nullable();
            $table->text('user_agent')->nullable();

            // Hasil deteksi ringan dari user agent, tanpa dependency tambahan
            $table->string('device_type', 20)->default('unknown'); // desktop, mobile, tablet, bot, unknown
            $table->string('browser', 30)->default('Unknown');

            $table->timestamps();

            $table->index('created_at');
            $table->index('ip_address');
            $table->index('url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('visits');
    }
};