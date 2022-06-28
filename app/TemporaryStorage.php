<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TemporaryStorage extends Model
{
    protected $guarded = ['id'];

    public function Distributor()
    {
        return $this->belongsTo(Distributor::class);
    }

    public function Clothes()
    {
        return $this->belongsTo(Clothes::class);
    }
}
