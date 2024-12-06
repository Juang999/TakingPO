<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    protected $fillable = ['type'];

    public function Clothes()
    {
        return $this->hasMany(Clothes::class, 'type_id', 'id');
    }
}
