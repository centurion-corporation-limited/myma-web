<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Kyslik\ColumnSortable\Sortable;

class Adinvoices extends Model
{
    use Sortable;

    protected $connection = "mysql_2";
    protected $table = "ad_invoices";
    public $timestamps = true;

    protected $fillable = [
        'user_id', 'ad_id', 'type', 'impressions', 'price', 'status', 'customer_no', 'sales_person', 'payment_mode', 'credit_term', 'due_date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ad()
    {
        return $this->belongsTo(Advertisement::class, 'ad_id');
    }

}
