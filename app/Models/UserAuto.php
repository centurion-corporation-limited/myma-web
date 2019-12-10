<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Traits\EncryptableTrait;
use Carbon\Carbon;


class UserAuto extends Model
{

    use EncryptableTrait;

    protected $encryptable = [
        'fin_no'
    ];
    protected $connection = "mysql";

    protected $table = "user_auto";
    public $timestamps = false;

    protected $fillable = [
        'name','fin_no','country_id', 'dormitory_id', 'street_address', 'dob', 'wp_expiry', 'nationality', 'dorm'
    ];
    public function getDobformattedAttribute(){
      $dob = $this->dob;
      if($dob != '')
        $dob = Carbon::parse($dob)->format('d/m/Y');
      return $dob;

    }

    public function getWpexpiryformattedAttribute(){
      $dob = $this->wp_expiry;
      if($dob != '')
        $dob = Carbon::parse($dob)->format('d/m/Y');
      return $dob;

    }
}
