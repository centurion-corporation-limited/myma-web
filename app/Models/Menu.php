<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Kyslik\ColumnSortable\Sortable;

class Menu extends Model
{
  use Sortable;

    protected $connection = "mysql_2";
    protected $table = "menus";
    public $timestamps = false;

    protected $fillable = [
        'name', 'slug', 'active', 'order', 'name_ta','name_bn','name_mn', 'name_th', 'access', 'icon', 'category_id', 'type'
    ];
    public function category()
    {
        return $this->belongsTo(MenuCategory::class);
    }
}
