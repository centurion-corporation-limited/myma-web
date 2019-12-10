<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class OrderReject extends Model
{
    protected $connection = "mysql_2";
    protected $table = "order_reject";
    public $timestamps = true;

    protected $fillable = [
        'order_id', 'user_id'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
