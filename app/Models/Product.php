<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = ['id'];

    public function Photo()
    {
        return $this->hasMany(Photo::class, 'product_id', 'id');
    }

    public function BufferProduct()
    {
        return $this->hasOne(BufferProduct::class, 'clothes_id', 'id');
    }
}
