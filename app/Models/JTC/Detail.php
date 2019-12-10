<?php

namespace App\Models\JTC;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Kyslik\ColumnSortable\Sortable;

class Detail extends Model
{
    use Sortable;
    protected $connection = "mysql_2";
    protected $table = "jtc_topic";
    public $timestamps = true;

    protected $fillable = [
        'title', 'event_id', 'image', 'content', 'type','order', 'language', 'created_by', 'share', 'author_image', 'publish', 'menu_type'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function content($lang = false)
    {
        if($lang){
            return $this->hasOne(TopicLang::class, 'topic_id')->where('language', $lang);
        }else{
            return $this->hasOne(TopicLang::class, 'topic_id');
        }
    }

    public function category($lang = false)
    {
        if($lang){
            return $this->belongsTo(CategoryLang::class, 'category_id', 'category_id')->where('language', $lang);
        }else{
            return $this->belongsTo(CategoryLang::class, 'category_id', 'category_id');
        }
    }

    public function lang_content()
    {
        return $this->hasMany(DetailLang::class, 'topic_id');
    }

    public function likes()
    {
        return $this->hasMany(Likes::class,'topic_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class,'topic_id');
    }

    public function lang_mn()
    {
        return $this->hasOne(DetailLang::class, 'topic_id')->where('language', 'mandarin');
    }

    public function lang_en()
    {
        return $this->hasOne(DetailLang::class, 'topic_id')->where('language', 'english');
    }

    public function lang_ta()
    {
        return $this->hasOne(DetailLang::class, 'topic_id')->where('language', 'tamil');
    }

    public function lang_th()
    {
        return $this->hasOne(DetailLang::class, 'topic_id')->where('language', 'thai');
    }

    public function lang_bn()
    {
        return $this->hasOne(DetailLang::class, 'topic_id')->where('language', 'bengali');
    }
}
