<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotifyUser extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $connection = "mysql_2";
    protected $table = "notify_user";
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'notify_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function notify()
    {
        return $this->belongsTo(Notification::class);
    }
}
