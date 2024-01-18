<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

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

    public function TableName()
    {
        return $this->hasOne(TableName::class);
    }

    public function Transaction()
    {
        return $this->hasMany(Transaction::class);
    }

    public function TemporaryStorage()
    {
        return $this->hasMany(TemporaryStorage::class);
    }

    public function PartnerAddress()
    {
        return $this->hasOne(PartnerAddress::class);
    }

    public function Agent()
    {
        return $this->hasMany(Agent::class);
    }

    public function PartnerGroup()
    {
        return $this->belongsTo(PartnerGroup::class);
    }

    public function MutifStoreMaster()
    {
        return $this->hasMany(MutifStoreMaster::class);
    }

    public function Phone()
    {
        return $this->hasMany(Phone::class);
    }

    public function Area()
    {
        return $this->belongsTo(Area::class);
    }

    public function User()
    {
        return $this->hasOne(User::class, 'partner_id', 'id');
    }
}
