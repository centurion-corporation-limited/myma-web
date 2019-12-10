<?php

namespace App\Models\JTC;

use Illuminate\Database\Eloquent\Model;
use App\User;

class CenterLang extends Model
{
    protected $connection = "mysql_2";
    protected $table = "jtc_center_locale";
    public $timestamps = false;

    protected $fillable = [
        'title', 'center_id', 'language', 'content'
    ];

    // public function type()
    // {
    //     return $this->belongsTo(Type::class);
    // }

}
