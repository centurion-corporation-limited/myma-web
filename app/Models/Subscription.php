<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Kyslik\ColumnSortable\Sortable;

class Subscription extends Model
{
  use Sortable;

    protected $connection = "mysql_2";
    protected $table = "subscription";
    public $timestamps = true;

    protected $fillable = [
        'item_id', 'order_id', 'breakfast','lunch', 'dinner', 'user_id', 'delivery_date', 'b_status', 'l_status', 'd_status', 'b_allowed', 'l_allowed',
        'd_allowed'
    ];

    public function item(){
        return $this->belongsTo(FoodMenu::class, 'item_id');
    }

    public function order(){
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function bstatus(){
        return $this->belongsTo(Status::class, 'b_status');
    }

    public function lstatus(){
        return $this->belongsTo(Status::class, 'l_status');
    }

    public function dstatus(){
        return $this->belongsTo(Status::class, 'd_status');
    }
}
