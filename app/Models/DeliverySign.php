<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class DeliverySign extends Model
{

    protected $connection = "mysql_2";
    protected $table = "delivery_sign";
    public $timestamps = true;

    protected $fillable = [
        'subscription_id', 'batch_id', 'name','sign', 'item_id', 'type'
    ];

    // public function restaurant(){
    //     return $this->belongsTo(Restaurant::class, 'restaurant_id');
    // }
    //
    // public function course(){
    //     return $this->belongsTo(FoodCourse::class, 'course_id');
    // }
    //
    // public function tags(){
    //     return $this->hasMany(FoodTag::class, 'food_id');
    // }

}
