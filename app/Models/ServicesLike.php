<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Models\Services;


class ServicesLike extends Model
{
    protected $connection = "mysql_2";
    protected $table = "services_likes";
    public $timestamps = [ "created_at" ];

    protected $fillable = [
        'service_id', 'user_id'
    ];

    public function setUpdatedAtAttribute($value)
    {
        // to Disable updated_at
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Services::class);
    }
}
