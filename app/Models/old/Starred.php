<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Starred extends Model
{
    protected $connection = "mysql_2";
    protected $table = "starred";
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'ref_id', 'type'
    ];

}
