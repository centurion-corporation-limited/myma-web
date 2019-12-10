<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Kyslik\ColumnSortable\Sortable;

class Merchant extends Model
{
  use Sortable;

    protected $connection = "mysql_2";
    protected $table = "flexm_merchant";
    public $timestamps = true;

    protected $fillable = [
        'merchant_name', 'wallet_type_indicator', 'merchant_category_code', 'mid', 'status', 'merchant_code', 'created_by', 'user_id', 'location',
        'merchant_share', 'myma_transaction_share', 'type', 'qr_code', 'start_date', 'frequency', 'revenue_model', 'v_cost_type', 'product_type',
        'merchant_address_1', 'merchant_address_2', 'merchant_address_3', 'gst', 'gst_number', 'naanstap_share', 'subscription_limit'
    ];

    public function dormitory(){
        return $this->belongsTo(Dormitory::class, 'dormitory_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function terminal(){
        return $this->hasOne(Terminal::class, 'merchant_id')->where('payment_mode', 2);
    }

    public function getMerchantNameAttribute() {
      return ucfirst($this->attributes['merchant_name']);
    }

    public function account(){
        return $this->hasOne(Account::class, 'merchant_id', 'id')->where('merchant_type', 'flexm');
    }

    public function terminals(){
        return $this->hasMany(Terminal::class, 'merchant_id');
    }

}
