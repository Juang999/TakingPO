<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;

class Distributor extends Model
{
    use SoftDeletes, LogsActivity;

    protected $guarded = ['id'];

    protected static $logName = 'system';

    protected static $logAttributes = ['name', 'phone'];

    public function getActivitylogOptions()
    {
        return LogOptions::defaults()
        ->setDescriptionForEvent(fn(string $eventName) => "user has been {$eventName}");
    }

    // protected $hidden = ['created_at', 'updated_at'];

    public function Order()
    {
        return $this->hasMany(Order::class, 'client_id', 'id');
    }

    public function PartnerAddress()
    {
        return $this->hasOne(PartnerAddress::class);
    }

    // public function Agent()
    // {
    //     return $this->hasMany(Agent::class);
    // }

    public function PartnerGroup()
    {
        return $this->belongsTo(PartnerGroup::class);
    }

    public function MutifStoreMaster()
    {
        return $this->hasMany(MutifStoreMaster::class, 'distributor_id', 'id');
    }

    public function Agent()
    {
        return $this->hasMany('App\Models\Distributor', 'distributor_id', 'id');
    }

    // public function User()
    // {
    //     return $this->hasOne(User::class, 'partner_id', 'id');
    // }
}
