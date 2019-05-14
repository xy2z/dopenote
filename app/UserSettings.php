<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class UserSettings extends Model {

	public $fillable = ['user_id'];

	/**
	 * Get user settings for a user
	 * If not exists, get default user settings.
	 *
	 */
	static public function get(int $user_id) : object {
		$user_settings = UserSettings::where('user_id', $user_id)->first();
		if (!$user_settings) {
			// Defaults.
			$user_settings = Config::get('app.default_user_settings');
		}

		return $user_settings;
	}

	/**
	 * Belongs to user
	 *
	 */
	public function user() {
		return $this->belongsTo(User::class);
	}

}
