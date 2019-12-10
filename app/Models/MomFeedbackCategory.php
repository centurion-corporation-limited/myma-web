<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Kyslik\ColumnSortable\Sortable;

class MomFeedbackCategory extends Model
{
  use Sortable;

    protected $connection = "mysql_2";
    protected $table = "mom_feedback_category";
    public $timestamps = true;

    protected $fillable = [
        'name'
    ];
}
