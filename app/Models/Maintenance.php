<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Kyslik\ColumnSortable\Sortable;

class Maintenance extends Model
{
  use Sortable;
  
    protected $connection = "mysql_2";
    protected $table = "maintenance";
    public $timestamps = true;
    protected $dates = ['completed_at'];

    protected $fillable = [
        'user_id', 'comments', 'send_to', 'fin', 'status_id', 'dormitory_id', 'photo_1', 'photo_2','photo_3',
        'photo_4','photo_5', 'location', 'remarks', 'logged_by', 'completed_at'
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

    public function files()
    {
        return $this->hasMany(File::class, 'ref_id')->where('type', 'maintenance');
    }
}
