<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Phone extends Model
{
    use LogsActivity;

    protected $guarded = ['id'];

    public function Distributor()
    {
        return $this->belongsTo(Distributor::class);
    }
}
