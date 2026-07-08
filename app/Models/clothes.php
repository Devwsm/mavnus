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
        return $this->belongsTo(Product::class, 'product_id', 'id_product');
    }

    public function variants()
    {
        return $this->hasMany(ClothesVariant::class, 'clothes_id', 'id_clothes');
    }
}