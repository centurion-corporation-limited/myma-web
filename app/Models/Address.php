<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;


class Address extends Model
{

    protected $connection = "mysql_2";

    protected $table = "addresses";
    public $timestamps = false;

    protected $fillable = [
        'user_id','address','block', 'latitude', 'longitude'
    ];

}
