<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BufferProduct extends Model
{
    protected $guarded = ['id'];

    public function Size()
    {
        return $this->belongsTo(Size::class);
    }

    public function Clothes()
    {
        return $this->belongsTo(Clothes::class);
    }
}
