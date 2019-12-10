<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;


class FcmToken extends Model
{

    protected $connection = "mysql_2";

    protected $table = "tokens";
    public $timestamps = false;

    protected $fillable = [
        'user_id','fcm_token'
    ];

    public function user(){
      return $thiw->belongsTo(User::class);
    }
 
}
