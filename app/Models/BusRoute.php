<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class BusRoute extends Model
{
    protected $connection = "mysql_2";

    protected $table = "bus_routes";
    public $timestamps = false;

    protected $fillable = [
            'ServiceNo',
            'Direction',
            'Operator',
            'StopSequence',
            'BusStopCode',
            'Distance',
            'WD_FirstBus',
            'WD_LastBus',
            'SAT_FirstBus',
            'SAT_LastBus',
            'SUN_FirstBus',
            'SUN_LastBus',
    ];

}
