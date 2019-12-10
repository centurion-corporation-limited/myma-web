<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Impression extends Model
{

    protected $connection = "mysql_2";
    protected $table = "impressions";
    public $timestamps = true;

    protected $fillable = [
        'ad_id', 'impressions'
    ];

}
