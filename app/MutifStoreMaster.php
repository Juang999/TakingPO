<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MutifStoreMaster extends Model
{
    protected $guarded = ['id'];

    public function Distributor()
    {
        return $this->belongsTo(Distributor::class);
    }

    public function MutifStoreAddress()
    {
        return $this->hasMany(MutifStoreAddress::class);
    }
}
