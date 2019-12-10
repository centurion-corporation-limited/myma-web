<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Share extends Model
{
    protected $connection = "mysql_2";
    protected $table = "share";
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'merchant_share'
    ];

}
