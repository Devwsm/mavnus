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
        Schema::table('accounts', function (Blueprint $table) {
            // Nama asli staff, dipakai buat sapaan personal di dashboard.
            // Nullable dulu biar aman buat akun lama yang belum punya nama;
            // diisi 'Owner' di bawah lewat data migration.
            $table->string('name')->nullable()->after('username');

            // Buat nonaktifkan staff yang resign tanpa hapus datanya
            // (histori pesanan/log yang pernah dia pegang tetap ke-track).
            $table->boolean('is_active')->default(true)->after('role');
        });

        // Data migration: akun lama role-nya 'admin' -> disamakan jadi 'owner'
        // (akses penuh, role name lama dianggap alias owner).
        DB::table('accounts')->where('role', 'admin')->update(['role' => 'owner']);

        // Isi nama default buat akun yang belum punya nama, biar gak kosong.
        DB::table('accounts')->whereNull('name')->update(['name' => 'Owner']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn(['name', 'is_active']);
        });
    }
};