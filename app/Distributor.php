<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Distributor extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    // protected $hidden = ['created_at', 'updated_at'];

    public function TableName()
    {
        return $this->hasOne(TableName::class);
    }

    public function Transaction()
    {
        return $this->hasMany(Transaction::class);
    }

    public function TemporaryStorage()
    {
        return $this->hasMany(TemporaryStorage::class);
    }

    public function PartnerAddress()
    {
        return $this->hasOne(PartnerAddress::class);
    }

    public function Agent()
    {
        return $this->hasMany(Agent::class);
    }

    public function PartnerGroup()
    {
        return $this->belongsTo(PartnerGroup::class);
    }

    public function MutifStoreMaster()
    {
        return $this->hasMany(MutifStoreMaster::class);
    }
}
