<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Topic extends Model
{
    protected $connection = "mysql_2";
    protected $table = "topics";
    public $timestamps = false;

    protected $fillable = [
        'title', 'share','description', 'image'
    ];

    public function setUpdatedAtAttribute($value)
    {
        // to Disable updated_at
    }

    public function likes()
    {
        return $this->hasMany(Likes::class, 'ref_id')->where('type', '=', 'topic');
    }

    public function forum()
    {
        return $this->hasMany(Forum::class);
    }
}
