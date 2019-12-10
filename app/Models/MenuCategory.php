<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Kyslik\ColumnSortable\Sortable;

class MenuCategory extends Model
{
    use Sortable;

    protected $connection = "mysql_2";
    protected $table = "menu_category";
    public $timestamps = false;

    protected $fillable = [
        'name'
    ];

}
