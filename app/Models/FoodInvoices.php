<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class FoodInvoices extends Model
{
    protected $connection = "mysql_2";
    protected $table = "food_invoices";
    public $timestamps = true;

    protected $fillable = [
        'user_id', 'status', 'notes', 'user_type', 'total', 'batch_id'
    ];

    public function merchant()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

}
