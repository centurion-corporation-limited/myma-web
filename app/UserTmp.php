<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Kodeine\Acl\Traits\HasRole;
use Cmgmyr\Messenger\Traits\Messagable;
use App\Traits\EncryptableTrait;
use App\Models\UserProfileTmp;
use App\Models\Share;
use Illuminate\Database\Eloquent\SoftDeletes;
// use Kodeine\Acl\Traits\HasPermission;
use Kyslik\ColumnSortable\Sortable;

class UserTmp extends Authenticatable
{
    use HasRole, Messagable, Notifiable;
    use EncryptableTrait;
    use Sortable;
    
    protected $table = [
        'users_tmp'
    ];
    
    protected $encryptable = [
        'email'
    ];

    protected  $connection = "mysql";
    public $timestamps = true;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'language', 'blocked', 'fcm_token', 'qr_code', 'type', 'register_by', 'flexm_error',
        'otp', 'dormitory_id', 'email_confirm_key', 'password_retry', 'last_logged', 'token', 'uid', 'singx_account', 'flexm_account',
        'flexm_error_text', 'flexm_cron', 'number_verified', 'wp_expiry_captured', 'wp_image_captured', 'address_captured',
        'name_captured', 'doc_verification', 'wallet_creation'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token','otp', 'email_confirm_key'
    ];

    public function profile()
    {
        return $this->hasOne(UserProfileTmp::class);
    }

    public function share()
    {
        return $this->hasOne(Share::class);
    }

    public function service_comment()
    {
        return $this->hasMany(ServicesComment::class);
    }

    public function getNameAttribute()
    {
        return ucfirst($this->attributes['name']);
    }
}
