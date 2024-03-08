<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class SampleProductPhoto extends Model
{
    use SoftDeletes, LogsActivity;

    protected $guarded = ['id'];

    protected static $logUnguarded = true;

    protected static $logName = 'system';

    public function SampleProduct()
    {
        return $this->belongsTo(SampleProduct::class, 'sample_product_id', 'id');
    }
}
