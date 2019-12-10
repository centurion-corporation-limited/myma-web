<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Kyslik\ColumnSortable\Sortable;

class Plans extends Model
{
  use Sortable;
  
    protected $connection = "mysql_2";
    protected $table = "plans";
    public $timestamps = true;

    protected $fillable = [
        'type', 'price','impressions'
    ];

    public function ads()
    {
        return $this->hasMany(Advertisement::class, 'plan_id');
    }
}
