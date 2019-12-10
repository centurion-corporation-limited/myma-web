<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class TripReject extends Model
{
    protected $connection = "mysql_2";
    protected $table = "trip_reject";
    public $timestamps = true;

    protected $fillable = [
        'trip_id', 'user_id'
    ];

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
