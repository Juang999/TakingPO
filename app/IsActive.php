<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class IsActive extends Model
{
    use LogsActivity;

    protected static $logName = 'system';

    protected static $logAttributes = ['name'];

    protected $fillable = ['name'];
}
