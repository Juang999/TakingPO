<?php

namespace App;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MutifStoreMaster extends Model
{
    use SoftDeletes;

    protected static $logName = 'system';

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
