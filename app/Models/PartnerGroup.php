<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class PartnerGroup extends Model
{
    use LogsActivity;

    protected static $logName = 'system';

    protected static $logAttributes = ['prtnr_name', 'prtnr_code', 'prtnr_desc', 'discount'];

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
