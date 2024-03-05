<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SampleProductPhoto extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    public function SampleProduct()
    {
        return $this->belongsTo(SampleProduct::class, 'sample_product_id', 'id');
    }
}
