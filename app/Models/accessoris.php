<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class accessoris extends Model
{
    //
    protected $table = 'accessories';
    protected $primaryKey = 'id_accessory';
    protected $fillable = [
        'product_id',
        'type',
    ];

    public function product()
    {
        return $this->belongsTo(product::class, 'product_id', 'id_product');
    }

    // Label rapi buat ditampilin di dashboard/preview, misal "Keychain"
    public function getTypeLabelAttribute(): string
    {
        return ucfirst($this->type);
    }
}