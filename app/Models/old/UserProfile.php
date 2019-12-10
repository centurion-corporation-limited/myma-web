<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Blog
 *
 * @property integer $id
 * @property integer $user_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Blog whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Blog whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Blog whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Blog whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class UserProfile extends Model
{
    protected $table = 'user_profile';
    public $timestamps = false;
    protected  $connection = "mysql";

    protected $fillable = ['user_id', 'fin_no', 'phone', 'profile_pic', 'gender', 'dob', 'street_address', 'block', 'sub_block', 'floor_no',
    'unit_no', 'room_no' ,'zip_code', 'wp_expiry', 'wp_front', 'wp_back', 'dormitory_id'];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function dormitory()
    {
        return $this->belongsTo(Dormitory::class);
    }
}
