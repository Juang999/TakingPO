<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MutifStoreMaster extends Model
{
    protected $guarded = ['id'];

    public function Agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function MutifStoreAddress()
    {
        return $this->hasMany(MutifStoreAddress::class);
    }
}
