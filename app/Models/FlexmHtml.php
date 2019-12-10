<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class FlexmHtml extends Model
{
    protected $connection = "mysql_2";

    protected $table = "flexm_html";
    public $timestamps = false;

    protected $fillable = [
        'user_id','html'
    ];
}
