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

    public function getPath() {
      return $this->attributes['path'];
    }

    public function getPathAttribute() {
      return url($this->attributes['path']);
    }
}
