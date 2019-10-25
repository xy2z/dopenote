<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Note;

class Notebook extends Model {
	protected $fillable = ['user_id', 'title', 'sort_order'];


	public function notes() {
		return $this->hasMany(Note::class);
	}

	public static function get_last_sort_order(int $user_id) {
		return static::where('user_id', $user_id)->max('sort_order');
	}

	/**
	 * Check if $notebook_id belongs to $user_id
	 *
	 * @param int $notebook_id
	 * @param int $user_id
	 *
	 * @return bool
	 */
	public static function belongs_to_user(int $notebook_id, int $user_id) : bool {
		return !is_null(
			static::where('id', $notebook_id)
			->where('user_id', $user_id)
			->first()
		);
	}

	/**
	*The user of the notebook
	*
	*@return \Illuminate\Database\Eloquent\Relations\belongsTo
	*/

	public function user() {
		return $this->belongsTo(User::class);
	}

	/**
	*Get the path to the notebook
	*
	*@return string
	*/

	public static function path() {
		return "/notebook/{ notebook }";
	}
}
