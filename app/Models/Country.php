<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;


class Country extends Model
{

    protected $connection = "mysql_2";

    protected $table = "countries";
    public $timestamps = false;

    protected $fillable = [
        'alpha_2_code','alpha_3_code','en_short_name', 'nationality', 'short_name'
    ];

}
