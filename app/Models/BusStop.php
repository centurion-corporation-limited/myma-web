<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class BusStop extends Model
{
    protected $connection = "mysql_2";

    protected $table = "bus_stops";
    public $timestamps = false;

    protected $fillable = [
            'name',
            'road_name',
            'name_slug',
            'road_name_slug',
            'longitude',
            'latitude',
            'code'
    ];

}
