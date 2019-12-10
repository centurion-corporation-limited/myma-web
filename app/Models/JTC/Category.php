<?php

namespace App\Models\JTC;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Kyslik\ColumnSortable\Sortable;

class Category extends Model
{
    use Sortable;

    protected $connection = "mysql_2";
    protected $table = "jtc_category";
    public $timestamps = true;

    protected $fillable = [
        'title', 'image', 'order', 'created_by', 'language', 'active', 'center_id', 'type'
    ];

    public function content($lang = false)
    {
        if($lang){
            return $this->hasOne(CategoryLang::class, 'category_id')->where('language', $lang);
        }else{
            return $this->hasOne(CategoryLang::class, 'category_id');
        }
    }

    public function lang_content()
    {
        return $this->hasMany(CategoryLang::class, 'category_id');
    }

    public function main()
    {
        return $this->belongsTo(Center::class, 'center_id');
    }

}
