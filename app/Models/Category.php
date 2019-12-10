<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Kyslik\ColumnSortable\Sortable;

class Category extends Model
{
  use Sortable;
  
    protected $connection = "mysql_2";
    protected $table = "category";
    public $timestamps = false;

    protected $fillable = [
        'name', 'type_id', 'name_mn', 'name_bn', 'name_ta', 'name_th'
    ];

    public function type()
    {
        return $this->belongsTo(Type::class);
    }

}
