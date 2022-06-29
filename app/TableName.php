<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TableName extends Model
{
    protected $fillable = ['distributor_id', 'table_name'];

    protected $hidden = ['created_at', 'updated_at'];

    public function Distributor()
    {
        return $this->belongsTo(Distributor::class);
    }
}
