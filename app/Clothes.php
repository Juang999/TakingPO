<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Clothes extends Model
{
    use SoftDeletes, LogsActivity;

    protected $guarded = ['id'];

    protected static $logName = 'system';

    protected static $logAttributes = ['entity_name', 'article_name'];

    public function Type()
    {
        return $this->belongsTo(Type::class);
    }

    public function TemporaryStorage()
    {
        return $this->hasMany(TemporaryStorage::class);
    }

    public function TotalProduct()
    {
        return $this->hasMany(TotalProduct::class);
    }

    public function Image()
    {
        return $this->hasMany(Image::class);
    }

    public function BufferProduct()
    {
        return $this->hasMany(BufferProduct::class);
    }
}
