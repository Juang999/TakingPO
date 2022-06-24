<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TableName extends Model
{
    protected $fillable = ['user_id', 'table_name'];

    public function Distributor()
    {
        return $this->belongsTo(Distributor::class);
    }
}
