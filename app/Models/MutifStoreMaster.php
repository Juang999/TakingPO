<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MutifStoreMaster extends Model
{
    use SoftDeletes;

    protected static $logName = 'system';

    protected $guarded = ['id'];

    public function Agent()
    {
        return $this->belongsTo(Distributor::class, 'distributor_id', 'id');
    }

    public function MutifStoreAddress()
    {
        return $this->hasMany(MutifStoreAddress::class);
    }
}
