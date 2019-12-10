<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Type extends Model
{
    protected $connection = "mysql_2";
    protected $table = "type";
    public $timestamps = false;

    protected $fillable = [
        'name'
    ];

    // public function category()
    // {
    //     return $this->belongsTo(Category::class);
    // }

}
