<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class TrainingReview extends Model
{
    protected $table = "e_train_review";
    public $timestamps = true;

    protected $fillable = [
        'module_id', 'user_id', 'comment', 'for_user'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'for_user');
    }
}
