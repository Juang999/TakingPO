<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    protected $fillable = ['size'];

    public function BufferProduct()
    {
        return $this->hasMany(BufferProduct::class(), 'size_id', 'id');
    }
}
