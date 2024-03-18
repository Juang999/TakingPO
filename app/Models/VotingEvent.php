<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class VotingEvent extends Model
{
    use LogsActivity;

    protected $guarded = ['id'];

    protected static $logUnguarded = true;

    protected static $logName = 'system';
}
