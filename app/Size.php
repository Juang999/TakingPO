<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    protected $fillable = ['size'];

    protected $hidden = ['created_at', 'updated_at'];

    public function BufferProduct()
    {
        return $this->hasMany(BufferProduct::class);
    }
}
