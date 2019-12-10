<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
// use App\Models\UserProfile;
use Kyslik\ColumnSortable\Sortable;

class Servicess extends Model
{
  use Sortable;
  
    protected $connection = "mysql_2";
    protected $table = "services";
    public $timestamps = true;

    protected $fillable = [
        'type', 'image', 'share', 'author_image', 'publish', 'language', 'user_id', 'dormitory_id'
    ];
//'title', 'content','title_bn', 'content_bn','title_ta', 'content_ta','title_mn', 'content_mn',
// 'author', 'author_mn', 'author_ta', 'author_bn',
    // public function user()
    // {
    //     return $this->belongsTo(User::class, 'author_id');
    // }

    public function profile()
    {
        return $this->hasOne(UserProfile::class, 'user_id');
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function dormitory()
    {
        return $this->belongsTo(Dormitory::class, 'dormitory_id');
    }

    // public function author()
    // {
    //     return $this->belongsTo(User::class, 'author_id');
    // }

    public function likes()
    {
        return $this->hasMany(ServicesLike::class,'service_id');
    }

    public function comments()
    {
        return $this->hasMany(ServicesComment::class,'service_id');
    }

    public function lang_content()
    {
        return $this->hasMany(ServicesLang::class, 'services_id');
    }

    public function lang_mn()
    {
        return $this->hasOne(ServicesLang::class, 'services_id')->where('language', 'mandarin');
    }

    public function lang_en()
    {
        return $this->hasOne(ServicesLang::class, 'services_id')->where('language', 'english');
    }

    public function lang_ta()
    {
        return $this->hasOne(ServicesLang::class, 'services_id')->where('language', 'tamil');
    }

    public function lang_th()
    {
        return $this->hasOne(ServicesLang::class, 'services_id')->where('language', 'thai');
    }

    public function lang_bn()
    {
        return $this->hasOne(ServicesLang::class, 'services_id')->where('language', 'bengali');
    }

    // public static function getLikes($service_id)
    // {
    //     $table = Models::table('services_likes');
    //     return $table->where('service_id', $service_id)->get()->count();
    //     // return self::where('subject', 'like', $subjectQuery)->get();
    // }
}
