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
}
