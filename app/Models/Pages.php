<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Pages extends Model
{
  use Sortable;
  
    protected $connection = "mysql_2";
    protected $table = "pages";
    public $timestamps = false;

    protected $fillable = [
        'language', 'style', 'script'
    ];

    public function content($lang = false)
    {
        if($lang){
            return $this->hasOne(PageLang::class, 'page_id')->where('language', $lang);
        }else{
            return $this->hasOne(PageLang::class, 'page_id');
        }
    }

    public function lang_content()
    {
        return $this->hasMany(PageLang::class, 'page_id');
    }

}
