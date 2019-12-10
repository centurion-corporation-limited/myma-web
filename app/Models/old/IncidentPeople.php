<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class IncidentPeople extends Model
{
    protected $connection = "mysql_2";
    protected $table = "incident_peoples";
    public $timestamps = false;

    protected $fillable = [
        'incident_id', 'name', 'id_no'
    ];

    // public function vphotos()
    // {
    //     return $this->hasMany(IncidentPhoto::class,'people_id');
    // }
}
