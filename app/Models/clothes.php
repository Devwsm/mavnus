<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class clothes extends Model
{
    //
    protected $table = 'clothes';
    protected $primaryKey = 'id_clothes';
    protected $fillable = [
        'product_id',
        'color',
        'material',
    ];

    public function product()
    {
        return $this->belongsTo(product::class, 'product_id', 'id_product');
    }
}