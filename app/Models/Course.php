<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Kyslik\ColumnSortable\Sortable;

class Course extends Model
{
  use Sortable;
    
    protected $connection = "mysql_2";
    protected $table = "course";
    public $timestamps = true;
    protected $fillable = [
        'course_type','vendor_id', 'image', 'duration','duration_m' , 'duration_breakage', 'fee',
        'share', 'type', 'start_date', 'end_date', 'language'
    ];
// 'venue','audience', 'title', 'about', 'description','help_text', 'duration_label',
    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function content()
    {
        return $this->hasMany(Content::class, 'course_id');
    }

    public function likes()
    {
        return $this->hasMany(Likes::class, 'ref_id')->where('type', '=', 'course');;
    }

    public function lang_content()
    {
        return $this->hasMany(CourseLanguage::class, 'course_id');
    }

    public function course_mn()
    {
        return $this->hasOne(CourseLanguage::class, 'course_id')->where('language', 'mandarin');
    }

    public function course_en()
    {
        return $this->hasOne(CourseLanguage::class, 'course_id')->where('language', 'english');
    }

    public function course_ta()
    {
        return $this->hasOne(CourseLanguage::class, 'course_id')->where('language', 'tamil');
    }

    public function course_th()
    {
        return $this->hasOne(CourseLanguage::class, 'course_id')->where('language', 'thai');
    }

    public function course_bn()
    {
        return $this->hasOne(CourseLanguage::class, 'course_id')->where('language', 'bengali');
    }


}
