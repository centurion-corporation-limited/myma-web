<?php

namespace App\Models\JTC;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Kyslik\ColumnSortable\Sortable;

class Center extends Model
{
    use Sortable;

    protected $connection = "mysql_2";
    protected $table = "jtc_center";
    public $timestamps = true;

    protected $fillable = [
        'title', 'image', 'order', 'created_by', 'language', 'active', 'type'
    ];

    public function content($lang = false)
    {
        if($lang){
            return $this->hasOne(CenterLang::class, 'center_id')->where('language', $lang);
        }else{
            return $this->hasOne(CenterLang::class, 'center_id');
        }
    }

    public function lang_content()
    {
        return $this->hasMany(CenterLang::class, 'center_id');
    }

}
