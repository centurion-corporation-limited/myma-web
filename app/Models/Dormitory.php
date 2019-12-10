<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class Dormitory extends Model
{
    use SoftDeletes;
    use Sortable;

    protected $connection = "mysql_2";
    protected $table = "dormitory";
    public $timestamps = false;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name', 'manager_id', 'status_id'
    ];

    public function manager(){
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function status(){
        return $this->belongsTo(Status::class);
    }
}
