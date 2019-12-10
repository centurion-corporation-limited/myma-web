<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;


class FoodMenu extends Model
{
    use SoftDeletes;
    use Sortable;

    protected $connection = "mysql_2";
    protected $table = "food_menu";
    public $timestamps = false;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name', 'description', 'restaurant_id', 'base_price', 'price', 'category_id', 'course_id', 'image', 'is_veg', 'is_halal',
        'breakfast', 'lunch', 'dinner', 'type', 'total_orders', 'total_rating', 'published','recommended'
    ];

    public function restaurant(){
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }

    public function course(){
        return $this->belongsTo(FoodCourse::class, 'course_id');
    }

    public function tags(){
        return $this->hasMany(FoodTag::class, 'food_id');
    }

}
