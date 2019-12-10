<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;


class Account extends Model
{

    protected $connection = "mysql_3";

    protected $table = "accounts";
    public $timestamps = true;

    protected $fillable = [
        'merchant_id','merchant_type','bank_name','account_number', 'bank_address', 'bank_country', 'swift_code', 'routing_code'
    ];

}
