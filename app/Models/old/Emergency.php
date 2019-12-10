<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Emergency extends Model
{
    protected $connection = "mysql_2";
    protected $table = "emergency";
    public $timestamps = false;

    protected $fillable = [
        'name', 'value', 'category_id'
    ];

    public function setUpdatedAtAttribute($value)
    {
        // to Disable updated_at
    }

    public function category()
    {
        return $this->hasOne(Category::class);
    }

}
