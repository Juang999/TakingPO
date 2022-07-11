<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MutifStoreAddress extends Model
{
    protected $guarded = ['id'];

    public function MutifStoreMaster()
    {
        return $this->belongsTo(MutifStoreMaster::class);
    }
}
