<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class IncidentPhoto extends Model
{
    protected $connection = "mysql_2";
    protected $table = "incident_photos";
    public $timestamps = true;

    protected $fillable = [
        'incident_id', 'path', 'type', 'people_id'
    ];


}
