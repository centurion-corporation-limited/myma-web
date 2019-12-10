<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Kyslik\ColumnSortable\Sortable;

class Batch extends Model
{
  use Sortable;
  
    protected $connection = "mysql_2";

    protected $table = "food_batch";
    public $timestamps = true;

    protected $fillable = [
        'dormitory_id','batch_b','batch_l','batch_d', 'address', 'postal_code', 'batch_date'
    ];

    public function dormitory()
    {
        return $this->belongsTo(Dormitory::class, 'dormitory_id');
    }

}
