<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TotalProduct extends Model
{
    protected $guarded = ['id'];

    public function Clothes()
    {
        return $this->belongsTo(Clothes::class);
    }
}
