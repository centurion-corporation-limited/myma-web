<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoginHistory extends Model
{
    protected $connection = "mysql_2";
    protected $table = "login_history";
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'type', 'logged_time'
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

}
