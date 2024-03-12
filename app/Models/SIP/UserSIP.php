<?php

namespace App\Models\SIP;

use Illuminate\Database\Eloquent\Model;

class UserSIP extends Model
{
    protected $connection = 'sipconnection';

    protected $table = 'users';
}
