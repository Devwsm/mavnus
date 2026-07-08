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
    ];

    public function product()
    {
        return $this->belongsTo(product::class, 'product_id', 'id_product');
    }
}