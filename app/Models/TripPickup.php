<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class TripPickup extends Model
{
    protected $connection = "mysql_2";
    protected $table = "trip_pickup";
    public $timestamps = false;

    protected $fillable = [
        'trip_id', 'pickup_id',
    ];

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function pickup()
    {
        return $this->belongsTo(Restaurant::class, 'pickup_id');
    }

    public function tripOrders()
    {
        return $this->hasMany(TripOrders::class, 'trip_pick_id');
    }

}
