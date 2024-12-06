<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BufferProduct extends Model
{
    protected $guarded = ['id'];

    public function Size()
    {
        return $this->belongsTo(Size::class(), 'size_id', 'id');
    }

    public function Clothes()
    {
        return $this->belongsTo(Clothes::class(), 'clothes_id', 'id');
    }

    public function Product()
    {
        return $this->belongsTo(Product::class(), 'clothes_id', 'id');
    }
}
