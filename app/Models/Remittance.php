<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Kyslik\ColumnSortable\Sortable;

class Remittance extends Model
{
  use Sortable;
  
    protected $connection = "mysql_2";
    protected $table = "remittance";
    public $timestamps = true;

    protected $fillable = [
        'hash_id', 'provider', 'delivery_method', 'receive_country', 'status', 'payout_agent', 'customer_fx', 'send_currency', 'send_amount', 'customer_fixed_fee',
        'total_transaction_amount', 'receive_currency', 'receive_amount', 'crossrate', 'provider_amount_fee_currency', 'provider_amount_fee','provider_exchange_rate',
        'send_amount_rails_currency', 'send_amount_rails', 'send_amount_before_fx', 'send_amount_after_fx', 'routing_params', 'ref_id', 'transaction_code',
        'sender_first_name','sender_middle_name', 'sender_last_name', 'sender_mobile_number', 'ben_first_name','ben_middle_name','ben_last_name',
        'date_added','date_expiry','status_last_updated','create_request','create_response','confirm_response','remarks', 'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
