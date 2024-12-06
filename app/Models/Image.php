<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = ['clothes_id', 'photo'];

    public function Clothes()
    {
        return $this->belongsTo(Clothes::class, 'clothes_id', 'id');
    }
}
