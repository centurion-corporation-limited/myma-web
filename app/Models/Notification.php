<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $connection = "mysql_2";
    protected $table = "notifications";
    public $timestamps = true;

    protected $fillable = [
        'title', 'message', 'message_bn', 'message_mn', 'message_ta', 'message_th', 'type', 'ref_id',
        'user_id', 'created_by', 'link', 'send_at', 'message_type', 'file'  
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
