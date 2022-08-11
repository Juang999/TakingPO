<?php

namespace App;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;

class MutifStoreMaster extends Model
{
    use LogsActivity, SoftDeletes;

    protected static $logName = 'system';

    protected static $logAttributes = ['entity_name', 'article_name'];

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
