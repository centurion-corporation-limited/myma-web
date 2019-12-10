<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class MerchantCode extends Model
{
    protected $connection = "mysql_2";
    protected $table = "merchant_codes";
    public $timestamps = false;

    protected $fillable = [
        'code', 'title'
    ];

}
