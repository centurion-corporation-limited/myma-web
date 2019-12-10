<?php

namespace App\Models\JTC;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Kyslik\ColumnSortable\Sortable;

class Event extends Model
{
    use Sortable;

    protected $connection = "mysql_2";
    protected $table = "jtc_event";
    public $timestamps = true;

    protected $fillable = [
        'title', 'image', 'order', 'created_by', 'language', 'active', 'category_id', 'type'
    ];

    public function content($lang = false)
    {
        if($lang){
            return $this->hasOne(EventLang::class, 'event_id')->where('language', $lang);
        }else{
            return $this->hasOne(EventLang::class, 'event_id');
        }
    }

    public function lang_content()
    {
        return $this->hasMany(EventLang::class, 'event_id');
    }

    public function main()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function detail()
    {
        return $this->hasOne(Detail::class, 'event_id', 'id');
    }

}
