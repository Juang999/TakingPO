<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Image extends Model
{
    use SoftDeletes;

    protected $fillable = ['clohtes_id', 'image'];

    public function Clothes()
    {
        return $this->belongsTo(Clothes::class);
    }
}
