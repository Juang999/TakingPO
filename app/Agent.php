<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    protected $guarded = ['id'];

    public function Distributor()
    {
        return $this->belongsTo(Distributor::class);
    }

    public function MutifStoreMaster()
    {
        return $this->hasMany(MutifStoreMaster::class);
    }

    public function PartnerGroup()
    {
        return $this->belongsTo(PartnerGroup::class);
    }
}
