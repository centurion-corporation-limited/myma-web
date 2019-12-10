<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class FoodTag extends Model
{
    protected $connection = "mysql_2";
    protected $table = "food_tags";
    public $timestamps = false;

    protected $fillable = [
        'food_id', 'category_id'
    ];

    public function category(){
        return $this->belongsTo(FoodCategory::class, 'category_id');
    }

    public function food(){
        return $this->belongsTo(FoodMenu::class, 'food_id');
    }
}
