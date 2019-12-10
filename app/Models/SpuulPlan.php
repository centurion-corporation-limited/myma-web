<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class SpuulPlan extends Model
{
    use SoftDeletes;
    use Sortable;

    protected $connection = "mysql_2";
    protected $table = "spuul_plan";
    public $timestamps = true;

    protected $fillable = [
        'status', 'price','type', 'deleted_at', 'myma_share', 'spuul_share','list_order'
    ];

}
