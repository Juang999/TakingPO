<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Image extends Model
{
    use SoftDeletes, LogsActivity;

    protected $guarded = ['id'];

    public function Clothes()
    {
        return $this->belongsTo(Clothes::class);
    }
}
