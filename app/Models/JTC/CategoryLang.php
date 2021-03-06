<?php

namespace App\Models\JTC;

use Illuminate\Database\Eloquent\Model;
use App\User;

class CategoryLang extends Model
{
    protected $connection = "mysql_2";
    protected $table = "jtc_category_locale";
    public $timestamps = false;

    protected $fillable = [
        'title', 'category_id', 'language', 'content'
    ];

    // public function type()
    // {
    //     return $this->belongsTo(Type::class);
    // }

}
