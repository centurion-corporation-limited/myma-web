<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class ServicesLang extends Model
{

    protected $connection = "mysql_2";
    protected $table = "services_language";
    public $timestamps = false;

    protected $fillable = [
        'title', 'content', 'language', 'author' ,'services_id'
    ];

}
