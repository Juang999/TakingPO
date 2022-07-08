<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Clothes extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    public function Type()
    {
        return $this->belongsTo(Type::class);
    }

    public function TemporaryStorage()
    {
        return $this->hasMany(TemporaryStorage::class);
    }

    public function TotalProduct()
    {
        return $this->hasMany(TotalProduct::class);
    }

    public function Image()
    {
        return $this->hasMany(Image::class);
    }

    public function BufferProduct()
    {
        return $this->hasMany(BufferProduct::class);
    }
}
