<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BufferProduct extends Model
{
    protected $fillable = ['clothes_id', 'size_id', 'qty_avaliable', 'qty_process', 'qty_buffer'];

    public function Size()
    {
        return $this->belongsTo(Size::class);
    }

    public function Clothes()
    {
        return $this->belongsTo(Clothes::class);
    }
}
