<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Kyslik\ColumnSortable\Sortable;

class Transactions extends Model
{
  use Sortable;

    protected $connection = "mysql_2";
    protected $table = "transactions";
    public $timestamps = true;

    protected $fillable = [
        'type', 'ref_id', 'phone_no', 'wallet_user_name', 'transaction_date', 'transaction_amount', 'transaction_currency','food_merchant_id',
        'transaction_ref_no', 'transaction_status', 'transaction_code', 'mid', 'tid', 'merchant_name', 'payment_mode', 'naanstap_pay',
        'user_id', 'flexm_part', 'myma_part', 'myma_share', 'other_share', 'food_share', 'gst', 'status', 'response',
        'spuul_status', 'spuul_request', 'spuul_response', 'description', 'report_status', 'remarks'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'mid', 'mid');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'ref_id', 'id');
    }
    
    public function wallet()
    {
        return $this->belongsTo(Wallet::class, 'transaction_ref_no', 'transaction_ref_no');
    }

}
