<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class MomCategoryLang extends Model
{
    protected $connection = "mysql_2";
    protected $table = "mom_category_locale";
    public $timestamps = false;

    protected $fillable = [
        'title', 'category_id', 'language', 'content'
    ];

    // public function type()
    // {
    //     return $this->belongsTo(Type::class);
    // }

}
