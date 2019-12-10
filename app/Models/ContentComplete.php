<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Kyslik\ColumnSortable\Sortable;

class ContentComplete extends Model
{
  use Sortable;

    protected $connection = "mysql_2";
    protected $table = "course_content_complete";
    public $timestamps = true;

    protected $fillable = [
        'content_id', 'user_id'
    ];

    public function content()
    {
        return $this->belongsTo(Content::class, 'content_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
