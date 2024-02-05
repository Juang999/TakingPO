<?php

namespace App\Models;

use App\{Image, Clothes};
use Illuminate\Database\Eloquent\Model;

class Partnumber extends Model
{
    protected $fillable = ['clothes_id', 'image_id', 'partnumber'];

    public function Clothes()
    {
        return $this->belongsTo(Clothes::class);
    }

    public function Image()
    {
        return $this->belongsTo(Image::class);
    }
}
