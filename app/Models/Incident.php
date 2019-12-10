<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Kyslik\ColumnSortable\Sortable;

class Incident extends Model
{
  use Sortable;
  
    protected $connection = "mysql_2";
    protected $table = "incidents";
    public $timestamps = [ "created_at" ];

    protected $fillable = [
        'user_id', 'date', 'time', 'dormitory_id', 'location', 'details', 'photo_id', 'video_id', 'audio_id'
    ];

    public function people()
    {
        return $this->hasMany(IncidentPeople::class,'incident_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dormitory()
    {
        return $this->belongsTo(Dormitory::class);
    }

    public function photo()
    {
        return $this->belongsTo(File::class, 'photo_id');
    }

    public function audio()
    {
        return $this->belongsTo(File::class, 'audio_id');
    }

    public function video()
    {
        return $this->belongsTo(File::class, 'video_id');
    }

    public function setUpdatedAtAttribute($value)
    {
        // to Disable updated_at
    }

    public function getIdAttribute() {
      return sprintf("%05d", $this->attributes['id']);
    }

    // public function scopeKeyPhoto($query) {
    // return $query->join('incident_photos', function($join)
    // {
    //     $join->on('incident_photos.incident_id', '=', 'incident.id')->where('incident_peoples.type', 'key');

    // });
    // }
}
