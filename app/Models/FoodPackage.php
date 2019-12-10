<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Foodpackage extends Model
{
    protected $connection = "mysql_2";
    protected $table = "food_package";
    public $timestamps = false;

    protected $fillable = [
        'name', 'description', 'restaurant_id', 'breakfast', 'lunch', 'dinner', 'price'
    ];

    public function restaurant(){
        return $this->belongsTo(Restaurant::class);
    }

}
