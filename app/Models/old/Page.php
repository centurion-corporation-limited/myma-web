<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $connection = "mysql_2";
    protected $table = "pages";
    public $timestamps = false;

    protected $fillable = [
        'title', 'content', 'style', 'script'
    ];

}
