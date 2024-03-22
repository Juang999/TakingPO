<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class VotingScore extends Model
{
    use LogsActivity;

    protected $guarded = ['id'];

    protected static $logUnguarded = true;

    protected static $logName = 'system';

    public function Thumbnail()
    {
        return $this->hasOneThrough(SampleProductPhoto::class, SampleProduct::class, 'id', 'sample_product_id', 'sample_product_id', 'id');
    }
}
