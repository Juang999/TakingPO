<?php

namespace App;

use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Spatie\Activitylog\Traits\LogsActivity;

class Area extends Model
{
    use SoftDeletes, LogsActivity;

    protected $guarded = [];

    protected static $logName = 'system';

    protected static $logAttributes = ['code', 'name'];

    public function Distributor()
    {
        return $this->hasMany(Distributor::class);
    }

    public function PriceList()
    {
        return $this->hasMany(PriceList::class);
    }
}
