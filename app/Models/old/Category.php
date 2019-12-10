<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Category extends Model
{
    protected $connection = "mysql_2";
    protected $table = "category";
    public $timestamps = false;

    protected $fillable = [
        'name'
    ];

}
