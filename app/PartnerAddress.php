<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PartnerAddress extends Model
{
    protected $guarded = ['id'];

    public function Distributor()
    {
        return $this->belongsTo(Distributor::class);
    }
}
