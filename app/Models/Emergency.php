<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Kyslik\ColumnSortable\Sortable;

class Emergency extends Model
{
  use Sortable;
  
    protected $connection = "mysql_2";
    protected $table = "emergency";
    public $timestamps = false;

    protected $fillable = [
        'name','name_mn','name_bn','name_ta','name_th', 'value', 'category_id', 'type'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

}
