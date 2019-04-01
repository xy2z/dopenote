<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notebook extends Model {

	protected $fillable = ['user_id', 'title', 'sort_order'];


	public function notes() {
		return $this->hasMany(Note::class);
	}

	public static function get_last_sort_order(int $user_id) {
		return static::where('user_id', $user_id)->max('sort_order');
	}
}
