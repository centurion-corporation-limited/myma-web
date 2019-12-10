<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Kyslik\ColumnSortable\Sortable;

class Order extends Model
{
  use Sortable;

    protected $connection = "mysql_2";
    protected $dates = ['created_at', 'updated_at'];
    protected $table = "orders";
    public $timestamps = true;

    protected $fillable = [
        'transaction_id','user_id','status_id', 'batch_id', 'total', 'dormitory_id', 'phone_no', 'address',
         'naanstap', 'block_no', 'delivery_type', 'rating', 'delivery_date', 'delivery_time',
         'type','accepted', 'coupon_id', 'discount', 'driver_id', 'flexm'
    ];

    public function subs($item_id = ''){
      return $this->hasMany(Subscription::class);

      // if($item_id){
      //   return $this->hasMany(Subscription::class)->where('item_id', $item_id);
      // }else{
      //   return $this->hasMany(Subscription::class);
      // }

    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function driver(){
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function items(){
        return $this->hasMany(OrderItem::class);
    }

    public function restraAdd(){
        return @$this->hasMany(OrderItem::class)->first()->item->restaurant->address;
    }

    public function status(){
        return $this->belongsTo(Status::class);
    }

    public function transaction(){
        return $this->belongsTo(Transactions::class, 'transaction_id', 'transaction_ref_no');
    }

    public function dormitory(){
        return $this->belongsTo(Dormitory::class);
    }

}
