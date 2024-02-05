<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = ['id'];

    public function Photo()
    {
        return $this->hasManyThrough(Photo::class, Product::class, 'id', 'product_id', 'product_id', 'id');
    }
}
