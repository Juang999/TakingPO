<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Queue extends Model
{
    protected $fillable = ['clothes_id', 'queue'];

    // public function Clothes()
    // {
    //     return $this->belongsTo(Clothes::class);
    // }
}
