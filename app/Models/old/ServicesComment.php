<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class ServicesComment extends Model
{
    protected $connection = "mysql_2";
    protected $table = "services_comments";
    public $timestamps = ["created_at"];

    protected $fillable = [
        'service_id', 'comment', 'user_id',
    ];

    public function setUpdatedAtAttribute($value)
    {
        // to Disable updated_at
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    public function service()
    {
        return $this->belongsTo(Services::class);
    }

}
