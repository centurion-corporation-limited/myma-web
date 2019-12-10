<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Feedback extends Model
{
    protected $connection = "mysql_2";
    protected $table = "feedback";
    public $timestamps = [ "created_at" ];

    protected $fillable = [
        'name', 'email', 'phone', 'content', 'email_reply', 'type'
    ];

    public function setUpdatedAtAttribute($value)
    {
        // to Disable updated_at
    }
}
