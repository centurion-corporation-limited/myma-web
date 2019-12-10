<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Kyslik\ColumnSortable\Sortable;

class Payout extends Model
{
    use Sortable;
    protected $connection = "mysql_2";
    protected $table = "payout";
    public $timestamps = false;

    protected $fillable = [
        'merchant_id', 'amount', 'payout_date', 'value_date', 'transaction_id', 'remarks', 'status', 'start_date', 'type', 'quantity',
        'wallet_received_amount','revenue_deducted','txn_fee','cost_charged','net_payable','verified', 'payout_for', 'gst', 'exported'
    ];

    public function merchant(){
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }

}
