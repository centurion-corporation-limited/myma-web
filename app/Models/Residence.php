<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;


class Residence extends Model
{

    protected $connection = "mysql_2";

    protected $table = "countries_full";
    public $timestamps = false;

}
