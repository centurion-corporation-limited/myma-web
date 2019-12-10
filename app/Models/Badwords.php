<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Kyslik\ColumnSortable\Sortable;

class Badwords extends Model
{
  use Sortable;
  
    protected $connection = "mysql_2";
    protected $table = "bad_words";
    public $timestamps = false;

    protected $fillable = [
        'word', 'language'
    ];

}
