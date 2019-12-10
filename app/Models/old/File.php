<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class File extends Model
{
    protected $connection = "mysql_2";
    protected $table = "files";
    public $timestamps = false;

    protected $fillable = [
        'ref_id', 'type', 'path'
    ];
}
