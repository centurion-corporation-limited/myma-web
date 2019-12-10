<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Kyslik\ColumnSortable\Sortable;

class Sponsor extends Model
{
  use Sortable;

    protected $connection = "mysql_2";
    protected $table = "sponsor";
    public $timestamps = false;

    protected $fillable = [
        'name', 'phone', 'email', 'address', 'creator_id'
    ];

}
