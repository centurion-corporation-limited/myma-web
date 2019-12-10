<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Maintenance extends Model
{
    protected $connection = "mysql_2";
    protected $table = "maintenance";
    public $timestamps = true;

    protected $fillable = [
        'user_id', 'comments', 'send_to', 'fin', 'status_id', 'dormitory_id', 'photo_1', 'photo_2', 'location'
    ];

    public function getIdAttribute() {
      return sprintf("%05d", $this->attributes['id']);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dormitory()
    {
        return $this->belongsTo(Dormitory::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }
}
