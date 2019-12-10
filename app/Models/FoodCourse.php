<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Kyslik\ColumnSortable\Sortable;

class FoodCourse extends Model
{
  use Sortable;
  
    protected $connection = "mysql_2";
    protected $table = "food_course";
    public $timestamps = false;

    protected $fillable = [
        'name'
    ];

}
