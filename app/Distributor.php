<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Distributor extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    public function TableName()
    {
        return $this->hasOne(TableName::class);
    }
}
