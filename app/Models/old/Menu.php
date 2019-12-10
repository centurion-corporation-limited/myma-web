<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Menu extends Model
{
    protected $connection = "mysql_2";
    protected $table = "menus";
    public $timestamps = false;

    protected $fillable = [
        'name', 'slug', 'active', 'order'
    ];

}
