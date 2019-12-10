<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Kyslik\ColumnSortable\Sortable;
  
class Content extends Model
{
  use Sortable;
  
    protected $connection = "mysql_2";
    protected $table = "course_content";
    public $timestamps = false;

    protected $fillable = [
        'type', 'order', 'path', 'course_id', 'content_type', 'title','langauge'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function lang_content()
    {
        return $this->hasMany(ContentLang::class, 'content_id');
    }

    public function content_mn()
    {
        return $this->hasOne(ContentLang::class, 'content_id')->where('language', 'mandarin');
    }

    public function content_ta()
    {
        return $this->hasOne(ContentLang::class, 'content_id')->where('language', 'tamil');
    }

    public function content_bn()
    {
        return $this->hasOne(ContentLang::class, 'content_id')->where('language', 'bengali');
    }

    public function content_th()
    {
        return $this->hasOne(ContentLang::class, 'content_id')->where('language', 'thai');
    }

    public function files()
    {
        return $this->hasMany(ContentFile::class, 'content_id');
    }
}
