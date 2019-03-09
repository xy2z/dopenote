<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notebook extends Model
{
    public static function get_last_sort_order($user_id) {
    	return static::where('user_id', $user_id)->max('sort_order');
    }
}
