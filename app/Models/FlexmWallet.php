<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Kyslik\ColumnSortable\Sortable;

class FlexmWallet extends Model
{
    use Sortable;
    
    protected $connection = "mysql_2";
    protected $table = "transactions_wallet";
    public $timestamps = true;

    protected $fillable = [
        'user_id', 'from', 'to', 'amount', 'phone', 'country_code', 'message', 'transaction_id', 'status'
    ];

    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }

}
