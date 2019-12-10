<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Contact extends Model
{
    protected $connection = "mysql_2";

    protected $table = "contact_request";
    public $timestamps = [ "created_at" ];

    protected $fillable = [
        'name','email','phone','content'
    ];

}
