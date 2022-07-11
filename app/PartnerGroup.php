<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PartnerGroup extends Model
{
    protected $guarded = ['id'];

    public function Distributor()
    {
        return $this->hasMany(Distributor::class);
    }

    public function Agent()
    {
        return $this->hasMany(Agent::class);
    }
}
