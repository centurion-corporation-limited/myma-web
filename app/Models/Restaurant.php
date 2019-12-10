<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Kyslik\ColumnSortable\Sortable;

class Restaurant extends Model
{
  use Sortable;

    protected $connection = "mysql_2";
    protected $table = "restaurant";
    public $timestamps = false;

    protected $fillable = [
        'name', 'merchant_id','open_at', 'closes_at', 'address', 'image', 'fin_no', 'phone_no', 'gst_no', 'longitude', 'latitude' ,'blocked',
        'nea_number','bank_name','bank_number'
    ];

    public function merchant(){
        return $this->belongsTo(User::class, 'merchant_id');
    }

}
