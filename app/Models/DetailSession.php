<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailSession extends Model
{
    protected $guarded = ['id'];

    public function Session()
    {
        return $this->belongsTo(Session::class, 'session_id', 'id');
    }

    public function Product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
