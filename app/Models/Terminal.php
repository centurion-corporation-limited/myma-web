<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Terminal extends Model
{
    protected $connection = "mysql_2";
    protected $table = "flexm_terminal";
    public $timestamps = true;

    protected $fillable = [
        'merchant_id', 'tid', 'status', 'payment_mode', 'location', 'qr_code'
    ];


    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

}
