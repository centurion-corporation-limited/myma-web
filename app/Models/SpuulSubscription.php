<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Kyslik\ColumnSortable\Sortable;

class SpuulSubscription extends Model
{
  use Sortable;

    protected $connection = "mysql_2";
    protected $table = "spuul_subscriptions";
    public $timestamps = true;

    protected $fillable = [
        'user_id', 'transaction_id', 'start_date','end_date', 'status', 'plan_type', 'email', 'account_number', 'payment_done'
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
    //
    // public function order(){
    //     return $this->belongsTo(Order::class, 'order_id');
    // }
    //
    // public function bstatus(){
    //     return $this->belongsTo(Status::class, 'b_status');
    // }
    //
    // public function lstatus(){
    //     return $this->belongsTo(Status::class, 'l_status');
    // }
    //
    // public function dstatus(){
    //     return $this->belongsTo(Status::class, 'd_status');
    // }
}
