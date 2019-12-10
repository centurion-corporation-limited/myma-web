<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Option
 *
 * @property integer $id
 * @property string $name
 * @property string $value
 * @property boolean $autoload
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Option whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Option whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Option whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Option whereAutoload($value)
 * @mixin \Eloquent
 */
class Option extends Model
{
    protected $connection = "mysql_2";
    protected $table = 'options';
    protected $fillable = ['name', 'value'];

    public $timestamps = false;

    static public function getOption($name, $default = null)
    {
        $option = self::where('name', $name)->get()->first();

        if ($option) {
          if($option->value == "")
            return $default;
          return $option->value;
        }

        return $default;
    }

    static public function setOption($name, $value, $autoload = 0)
    {
        $option = self::where('name', $name)->get()->first();

        if (is_array($value)) {
            $value = serialize($value);
        }

        if ($option) {
            $option->value = $value;
            $option->save();
        } else {
            $option = new static;
            $option->name = $name;
            $option->value = $value;
            $option->save();
        }
    }
}
