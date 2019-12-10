<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Kyslik\ColumnSortable\Sortable;

class PayoutPackage extends Model
{
    use Sortable;
    protected $connection = "mysql_2";
    protected $table = "payout_packages";
    public $timestamps = false;

    protected $fillable = [
        'payout_id', 'package_1', 'package_2', 'package_3', 'package_4', 'total_amt', 'iso_share', 'wlc_share', 'naanstap_share'
    ];

    public function payout(){
        return $this->belongsTo(Payout::class, 'payout_id');
    }

}
