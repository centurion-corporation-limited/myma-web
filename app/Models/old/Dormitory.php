<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Dormitory extends Model
{
    protected $connection = "mysql_2";
    protected $table = "dormitory";
    public $timestamps = false;

    protected $fillable = [
        'name'
    ];

}
