<?php

namespace App\Http\Controllers;

use App\UserSettings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserSettingsRequest;
use Illuminate\Support\Facades\Config;


class UserSettingsController extends Controller {

	/**
	 * Show user settings form.
	 *
	 */
	public function show() {
		$user_settings = UserSettings::get(Auth::id());

		return view('user_settings', [
			'font_size' => $user_settings->font_size,
			'font_family' => $user_settings->font_family,
			'line_height' => $user_settings->line_height,
		]);
	}

	/**
	 * Save user settings form.
	 * Request is validated by UserSettingsRequest
	 *
	 */
	public function submit(UserSettingsRequest $request) {
		// User
		$user = Auth::user();
		$user->email = $request->email;

		// Change Password
		if ($request->filled('new_password')) {
			// Update password
			$user->password = Hash::make($request->new_password);
		}

		$user->save();


		// Editor Settings
		$user_settings = UserSettings::firstOrNew(['user_id' => Auth::id()]);
		$user_settings->font_size = $request->font_size;
		$user_settings->font_family = $request->font_family;
		$user_settings->line_height = $request->line_height;
		$user_settings->save();

		// Return
		return redirect()->back()->with('success', 'Settings are successfully saved.');

	}
}
