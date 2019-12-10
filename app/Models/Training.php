<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Models\Module;
use App\Models\TrainingComplete;
use Kyslik\ColumnSortable\Sortable;

class Training extends Model
{
  use Sortable;
  
    protected $table = "e_train";
    public $timestamps = true;

    protected $fillable = [
        'created_by', 'module_id', 'title', 'description', 'type', 'path'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class,'created_by');
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function read()
    {
        return $this->hasMany(TrainingComplete::class, 'e_train_id');
    }
}
