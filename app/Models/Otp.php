<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Otp extends Model
{
    protected $connection = "mysql_2";

    protected $table = "otp";
    public $timestamps = false;

    protected $fillable = [
        'phone','otp'
    ];
}
