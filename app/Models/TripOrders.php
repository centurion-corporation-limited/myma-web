<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class TripOrders extends Model
{
    protected $connection = "mysql_2";
    protected $table = "trip_orders";
    public $timestamps = false;

    protected $fillable = [
        'trip_pick_id', 'order_id'
    ];

    public function picktrip()
    {
        return $this->belongsTo(TripPickup::class, 'trip_pick_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

}
