<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Mpopular extends Model
{
    protected $connection = "mysql_2";
    protected $table = "menus_popular";
    public $timestamps = true;

    protected $fillable = [
        'menu_id', 'user_id', 'count'
    ];

    public function menu(){
        return $this->belongsTo(Menu::class);
    }
}
