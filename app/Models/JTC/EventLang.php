<?php

namespace App\Models\JTC;

use Illuminate\Database\Eloquent\Model;
use App\User;

class EventLang extends Model
{
    protected $connection = "mysql_2";
    protected $table = "jtc_event_locale";
    public $timestamps = false;

    protected $fillable = [
        'title', 'event_id', 'language', 'content'
    ];

    // public function type()
    // {
    //     return $this->belongsTo(Type::class);
    // }

}
