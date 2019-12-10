<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Kyslik\ColumnSortable\Sortable;

class MomTopic extends Model
{
  use Sortable;
  
    protected $connection = "mysql_2";
    protected $table = "mom_topic";
    public $timestamps = true;

    protected $fillable = [
        'title', 'category_id', 'image', 'content', 'type','order', 'language', 'created_by'
    ];

    public function content($lang = false)
    {
        if($lang){
            return $this->hasOne(MomTopicLang::class, 'topic_id')->where('language', $lang);
        }else{
            return $this->hasOne(MomTopicLang::class, 'topic_id');
        }
    }

    public function category($lang = false)
    {
        if($lang){
            return $this->belongsTo(MomCategoryLang::class, 'category_id', 'category_id')->where('language', $lang);
        }else{
            return $this->belongsTo(MomCategoryLang::class, 'category_id', 'category_id');
        }
    }

    public function lang_content()
    {
        return $this->hasMany(MomTopicLang::class, 'topic_id');
    }

}
