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
	public static function get(int $user_id) : object {
		// Merge default user settings with custom user settings.
		$settings = Config::get('app.default_user_settings');
		$user_settings = UserSettings::where('user_id', $user_id)->first();

		foreach ($settings as $key => $value) {
			if (isset($user_settings[$key])) {
				// Overwrite the default value with the user value.
				$settings->$key = $user_settings[$key];
			}
		}

		return $settings;
	}

	/**
	 * Belongs to user
	 *
	 */
	public function user() {
		return $this->belongsTo(User::class);
	}
}
