<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    protected $guarded = ['id'];

    public function Event()
    {
        return $this->belongsTo(Event::class, 'event_id', 'id');
    }

    public function DetailSession()
    {
        return $this->hasMany(DetailSession::class, 'session_id', 'id');
    }
}
