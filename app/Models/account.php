<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class account extends Model
{
    //
    protected $table = 'accounts';
    protected $primaryKey = 'id_account';
    protected $fillable = [
        'username',
        'name',
        'password',
        // Kolom role masih disimpan di database tapi sementara TIDAK dipakai membatasi akses:
        // semua akun staf aktif punya akses yang sama ke dashboard. Disimpan supaya role bisa
        // dihidupkan lagi nanti tanpa migrasi.
        'role',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Key session milik sesi staff (login lewat /crew-portal). Dipakai saat login, logout,
    // dan saat sesi dinyatakan tidak berlaku lagi, supaya daftarnya tidak tersebar di banyak file.
    public const SESSION_KEYS = ['login', 'user', 'name'];
}