<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Kodeine\Acl\Traits\HasRole;
use Cmgmyr\Messenger\Traits\Messagable;
use App\Traits\EncryptableTrait;
use App\Models\UserProfile;
use App\Models\Share;
use Illuminate\Database\Eloquent\SoftDeletes;
// use Kodeine\Acl\Traits\HasPermission;
use Kyslik\ColumnSortable\Sortable;
use App\Notifications\ResetPassword as ResetPasswordNotification;
class User extends Authenticatable
{
    use HasRole, Messagable, Notifiable;
    use EncryptableTrait;
    use Sortable;

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
        'otp', 'country_id', 'email_confirm_key', 'password_retry', 'last_logged', 'token', 'uid', 'singx_account', 'flexm_account',
        'flexm_error_text', 'flexm_cron', 'number_verified', 'flexm_cron_date', 'flexm_status', 'good_for_wallet', 'dormitory_id',
        'good_by', 'good_date', 'flexm_direct'
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
        return $this->hasOne(UserProfile::class);
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
    
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
