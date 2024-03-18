<?php

namespace App\Models;

use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Spatie\Activitylog\Traits\LogsActivity;

class FabricTexture extends Model
{
    use LogsActivity;

    protected static $logUnguarded = true;

    protected static $logName = 'system';

    protected $guarded = ['id', 'created_at', 'updated_at'];
}
