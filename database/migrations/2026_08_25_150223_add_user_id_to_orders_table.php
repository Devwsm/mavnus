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
        Schema::table('orders', function (Blueprint $table) {
            // Nullable - checkout guest (tanpa login) tetep harus bisa jalan,
            // cuma order_id-nya gak nyambung ke akun mana pun.
            // nullOnDelete: kalau suatu saat akun dihapus, riwayat order TETEP ada
            // (data transaksi/laporan gak boleh ikut hilang), cuma relasinya diputus.
            $table->foreignId('user_id')->nullable()->after('id_order')
                ->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
