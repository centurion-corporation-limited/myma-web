<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;


class ProductType extends Model
{

    protected $connection = "mysql_2";

    protected $table = "product_types";
    public $timestamps = false;

    protected $fillable = [
        'code','name'
    ];

}
