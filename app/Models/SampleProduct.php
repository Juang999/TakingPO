<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class SampleProduct extends Model
{
    use LogsActivity;

    protected $guarded = ['id'];

    protected static $logUnguarded = true;

    protected static $logName = 'system';

    public function PhotoSampleProduct()
    {
        return $this->hasMany(SampleProductPhoto::class, 'sample_product_id', 'id');
    }

    public function Thumbnail()
    {
        return $this->hasOne(SampleProductPhoto::class, 'sample_product_id', 'id');
    }
}
