<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Kyslik\ColumnSortable\Sortable;

class FoodMerchant extends Model
{
  use Sortable;

    protected $connection = "mysql_2";
    protected $table = "food_merchants";
    public $timestamps = true;

    protected $fillable = [
        'user_id', 'naanstap_share', 'start_date', 'frequency', 'bank_name', 'account_number', 'sub_limit', 'per_sub_price'
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function account(){
        return $this->hasOne(Account::class, 'merchant_id');
    }

}
