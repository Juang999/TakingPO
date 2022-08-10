<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class PartnerAddress extends Model
{
    use LogsActivity;

    protected $guarded = ['id'];

    protected static $logAttributes = ['*'];

    public function Distributor()
    {
        return $this->belongsTo(Distributor::class);
    }
}
