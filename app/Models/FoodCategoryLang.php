<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class FoodCategoryLang extends Model
{
    protected $connection = "mysql_2";
    protected $table = "food_category_locale";
    public $timestamps = false;

    protected $fillable = [
        'name', 'category_id', 'language'
    ];

}
