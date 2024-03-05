<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SampleProduct extends Model
{
    protected $guarded = ['id'];

    public function PhotoSampleProduct()
    {
        return $this->hasMany(SampleProductPhoto::class, 'sample_product_id', 'id');
    }
}
