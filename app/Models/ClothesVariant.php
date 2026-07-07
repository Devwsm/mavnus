<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClothesVariant extends Model
{
    //
    protected $primaryKey = 'id_clothes_variant';

    protected $fillable = [
        'clothes_id',
        'size',
        'stock',
    ];

    protected $casts = [
        'stock' => 'integer',
    ];

    public function clothes()
    {
        return $this->belongsTo(Clothes::class, 'clothes_id', 'id_clothes');
    }
}