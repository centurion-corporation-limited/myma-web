<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class OrderItem extends Model
{
    protected $connection = "mysql_2";

    protected $table = "order_items";
    public $timestamps = false;

    protected $fillable = [
        'order_id','item_id', 'item_price','restaurant_status_id','agent_status_id', 'quantity', 'name', 'sign', 'deliver_name', 'deliver_sign'
    ];

    public function item(){
        return $this->belongsTo(FoodMenu::class, 'item_id');
    }

    // public function item_restaurant($restaurant_id){
    //     return $this->belongsTo(FoodMenu::class, 'item_id');
    // }

    public function order(){
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function restaurant_status(){
        return $this->belongsTo(Status::class, 'restaurant_status_id');
    }

    public function agent_status(){
        return $this->belongsTo(Status::class, 'agent_status_id');
    }

}
