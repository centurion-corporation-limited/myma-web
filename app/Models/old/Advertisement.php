<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Advertisement extends Model
{
    protected $connection = "mysql_2";
    protected $table = "advertisement";
    public $timestamps = true;

    protected $fillable = [
        'title', 'path','type', 'start', 'end', 'description', 'slider_order', 'adv_type', 'report_whom', 'impressions'
    ];
}
