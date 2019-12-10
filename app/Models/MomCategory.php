<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Kyslik\ColumnSortable\Sortable;

class MomCategory extends Model
{
  use Sortable;
  
    protected $connection = "mysql_2";
    protected $table = "mom_category";
    public $timestamps = true;

    protected $fillable = [
        'title', 'image', 'order', 'created_by', 'language', 'active'
    ];

    public function content($lang = false)
    {
        if($lang){
            return $this->hasOne(MomCategoryLang::class, 'category_id')->where('language', $lang);
        }else{
            return $this->hasOne(MomCategoryLang::class, 'category_id');
        }
    }

    public function lang_content()
    {
        return $this->hasMany(MomCategoryLang::class, 'category_id');
    }

}
