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
        'role',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // 3 role yang valid di sistem. Dipakai buat validasi form & referensi
    // di tempat lain (dropdown pilih role, dsb) biar gak ada string "nyasar".
    public const ROLES = [
        'owner'         => 'Owner',
        'admin_produk'  => 'Admin Produk',
        'staff_pesanan' => 'Staff Pesanan',
    ];

    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }
}