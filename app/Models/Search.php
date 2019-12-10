<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Search extends Model
{
  use Sortable;
  
    protected $connection = "mysql_2";
    protected $table = "search";
    public $timestamps = false;

    protected $fillable = [
        'word', 'count'
    ];

}
