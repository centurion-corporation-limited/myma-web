<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class PageLang extends Model
{
    protected $connection = "mysql_2";
    protected $table = "page_locale";
    public $timestamps = false;

    protected $fillable = [
        'title', 'page_id', 'language', 'content'
    ];
}
