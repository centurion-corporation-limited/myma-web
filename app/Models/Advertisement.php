<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Kyslik\ColumnSortable\Sortable;

class Advertisement extends Model
{
  use Sortable;

    protected $connection = "mysql_2";
    protected $table = "advertisement";
    public $timestamps = true;

    protected $fillable = [
        'sponsor_id', 'path','type', 'start', 'end', 'description', 'slider_order', 'adv_type', 'report_whom', 'plan_id',
         'link', 'food_item', 'status'
    ];

    public function sponsor(){
        return $this->belongsTo(Sponsor::class);
    }
    
    public function food(){
        return $this->belongsTo(FoodMenu::class, 'food_item', 'id');
    }

    public function plan(){
        return $this->belongsTo(Plans::class);
    }

    public function impress(){
        return $this->hasOne(Impression::class, 'ad_id');
    }

    public function reportee(){
        return $this->belongsTo(User::class, 'report_whom');
    }

    public function invoice(){
        return $this->hasOne(Adinvoices::class, 'ad_id');
    }
}
