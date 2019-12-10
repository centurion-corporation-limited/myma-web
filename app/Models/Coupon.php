<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class Coupon extends Model
{
    use SoftDeletes;
    use Sortable;

    protected $connection = "mysql_2";
    protected $table = "food_coupons";
    public $timestamps = true;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'code', 'description', 'type','value', 'restra_type', 'created_by', 'expiry', 'merchant_id', 'item_ids'
    ];

    public function merchant(){
        return $this->belongsTo(User::class, 'merchant_id');
    }
    //
    // public function course(){
    //     return $this->belongsTo(FoodCourse::class, 'course_id');
    // }
    //
    // public function tags(){
    //     return $this->hasMany(FoodTag::class, 'food_id');
    // }

}
