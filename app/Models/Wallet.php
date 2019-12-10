<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Wallet extends Model
{
    protected $connection = "mysql_2";
    protected $table = "uploaded_wallet_report";
    public $timestamps = true;

    protected $fillable = [
        'mobile', 'wallet_user_name', 'transaction_date', 'transaction_amount', 'transaction_currency',
        'transaction_ref_no', 'transaction_status', 'transaction_code', 'mid', 'tid', 'merchant_name', 'payment_mode',
    ];

    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }

}
