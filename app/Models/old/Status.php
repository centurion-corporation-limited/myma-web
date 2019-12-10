<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Status extends Model
{
    protected $connection = "mysql_2";
    protected $table = "status";
    public $timestamps = false;

    protected $fillable = [
        'name'
    ];
}
