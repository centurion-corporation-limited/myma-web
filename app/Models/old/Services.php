<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
// use App\Models\UserProfile;

class Services extends Model
{
    protected $connection = "mysql_2";
    protected $table = "services";
    public $timestamps = true;

    protected $fillable = [
        'title', 'content', 'type', 'image', 'share'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class, 'user_id');
    }

    public function likes()
    {
        return $this->hasMany(ServicesLike::class,'service_id');
    }

    public function comments()
    {
        return $this->hasMany(ServicesComment::class,'service_id');
    }

    // public static function getLikes($service_id)
    // {
    //     $table = Models::table('services_likes');
    //     return $table->where('service_id', $service_id)->get()->count();
    //     // return self::where('subject', 'like', $subjectQuery)->get();
    // }
}
