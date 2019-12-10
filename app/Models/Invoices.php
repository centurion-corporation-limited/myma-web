<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Invoices extends Model
{
    protected $connection = "mysql_2";
    protected $table = "invoices";
    public $timestamps = true;

    protected $fillable = [
        'merchant_id', 'status_id', 'notes'
    ];

    public function merchant()
    {
        return $this->belongsTo(User::class, 'merchant_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

}
