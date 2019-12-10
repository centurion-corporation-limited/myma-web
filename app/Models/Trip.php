<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Kyslik\ColumnSortable\Sortable;

class Trip extends Model
{
  use Sortable;
  
    protected $connection = "mysql_2";
    protected $table = "trip";
    public $timestamps = true;

    protected $fillable = [
        'created_by', 'status_id', 'price', 'assigned_to', 'accepted_at', 'trip_date', 'trip_time'
    ];

    public function tripPickup()
    {
        return $this->hasMany(TripPickup::class, 'trip_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'ad_id');
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function pickups()
    {
        return $this->hasMany(TripPickup::class, 'trip_id');
    }

}
