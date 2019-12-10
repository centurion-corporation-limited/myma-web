<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class FlexmDoc extends Model
{
    protected $connection = "mysql_2";

    protected $table = "flexm_doc";
    public $timestamps = false;

    protected $fillable = [
        'phone_no','verified','document'
    ];
}
