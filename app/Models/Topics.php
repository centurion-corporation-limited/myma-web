<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Kyslik\ColumnSortable\Sortable;

class Topics extends Model
{
  use Sortable;

    protected $connection = "mysql_2";
    protected $table = "topics";
    public $timestamps = true;

    protected $fillable = [
        'title', 'share','description', 'image','title_mn','title_bn','title_ta','title_th','description_mn','description_bn','description_ta','description_th','language'
    ];

    // public function setUpdatedAtAttribute($value)
    // {
    //     // to Disable updated_at
    // }

    public function likes()
    {
        return $this->hasMany(Likes::class, 'ref_id')->where('type', '=', 'topic');
    }

    public function forum()
    {
        return $this->hasMany(Forum::class, 'topic_id');
    }

    public function lang_content()
    {
        return $this->hasMany(TopicLang::class, 'topic_id');
    }

    public function topic_mn()
    {
        return $this->hasOne(TopicLang::class, 'topic_id')->where('language', 'mandarin');
    }

    public function topic_en()
    {
        return $this->hasOne(TopicLang::class, 'topic_id')->where('language', 'english');
    }

    public function topic_ta()
    {
        return $this->hasOne(TopicLang::class, 'topic_id')->where('language', 'tamil');
    }

    public function topic_th()
    {
        return $this->hasOne(TopicLang::class, 'topic_id')->where('language', 'thai');
    }

    public function topic_bn()
    {
        return $this->hasOne(TopicLang::class, 'topic_id')->where('language', 'bengali');
    }
}
