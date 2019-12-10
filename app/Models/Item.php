<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Item extends Model
{
    protected $connection = "mysql_2";
    protected $table = "invoice_item";
    public $timestamps = false;

    protected $fillable = [
        'invoice_id', 'item', 'type', 'price', 'quantity'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoices::class);
    }
}
