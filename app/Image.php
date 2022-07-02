<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Image extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    public function Clothes()
    {
        return $this->belongsTo(Clothes::class);
    }
}
