<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Kyslik\ColumnSortable\Sortable;

class Feedback extends Model
{
  use Sortable;

    protected $connection = "mysql_2";
    protected $table = "feedback";
    public $timestamps = [ "created_at" ];

    protected $fillable = [
        'name', 'email', 'phone', 'content', 'email_reply', 'type', 'rating', 'category_id'
    ];

    public function setUpdatedAtAttribute($value)
    {
        // to Disable updated_at
    }

    public function replies()
    {
        return $this->hasMany(FeedbackReply::class);
    }

    public function category()
    {
        return $this->belongsTo(MomFeedbackCategory::class);
    }
}
