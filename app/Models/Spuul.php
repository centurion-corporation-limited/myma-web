<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Spuul extends Model
{
    protected $connection = "mysql_2";
    protected $table = "subscriptions";
    public $timestamps = true;

    protected $fillable = [
        'user_id', 'transaction_id', 'amount', 'plan_id', 'commission', 'start_date', 'end_date', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // public function plan()
    // {
        // return $this->belongsTo(SpuulPlan::class, 'plan_id');
    // }

}
