<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Phone extends Model
{
    use LogsActivity;

    protected $guarded = ['id'];

    protected static $logAttributes = ['*'];

    public function getActivitylogOptions()
    {
        return LogOptions::defaults()
        ->setDescriptionForEvent(fn(string $eventName) => "The number has been {$eventName}");
    }

    public function Distributor()
    {
        return $this->belongsTo(Distributor::class);
    }
}
