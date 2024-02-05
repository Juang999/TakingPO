<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PriceList extends Model
{
    protected $guarded = [];

    public function Area()
    {
        return $this->belongsTo(Area::class);
    }
}
