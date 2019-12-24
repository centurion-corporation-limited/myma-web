<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\UserProfile;

class RedeemUser extends Model
{
    protected $table = 'redeem_user';
    protected  $connection = "mysql";
    public $timestamps = false; 

    protected $fillable = [
        'name', 'mobile', 'user_id', 'ih_user_id', 'type', 'fin_no', 'myma_mobile', 
        'transaction_id' , 'click_redeem' , 'click_date' , 'wallet_credited_at' , 'credit_amount' ,  'status'
    ]; 

    public function profile()
    {
        return $this->hasOne(UserProfile::class, 'user_id', 'user_id');
    }

}