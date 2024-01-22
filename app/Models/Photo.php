<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    protected $guarded = ['id'];

    public function Product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
