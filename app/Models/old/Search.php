<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Search extends Model
{
    protected $connection = "mysql_2";
    protected $table = "search";
    public $timestamps = false;

    protected $fillable = [
        'word', 'count'
    ];

}
