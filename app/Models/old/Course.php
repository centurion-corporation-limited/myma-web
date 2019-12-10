<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Course extends Model
{
    protected $connection = "mysql_2";
    protected $table = "course";
    public $timestamps = false;
    protected $fillable = [
        'title', 'image', 'duration', 'about', 'description', 'duration_breakage', 'fee', 'share', 'help_text'
    ];

    public function setUpdatedAtAttribute($value)
    {
        // to Disable updated_at
    }

}
