<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Kyslik\ColumnSortable\Sortable;

class FoodCategory extends Model
{
  use Sortable;
    
    protected $connection = "mysql_2";
    protected $table = "food_category";
    public $timestamps = false;

    protected $fillable = [
        'name', 'slug', 'order','approved'
    ];

    public function content($lang = false)
    {
        if($lang){
            return $this->hasOne(FoodCategoryLang::class, 'category_id')->where('language', $lang);
        }else{
            return $this->hasOne(FoodCategoryLang::class, 'category_id');
        }
    }

    public function lang_content()
    {
        return $this->hasMany(FoodCategoryLang::class, 'category_id');
    }

}
