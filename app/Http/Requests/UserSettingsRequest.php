<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use App\Rules\PasswordMatch;


class UserSettingsRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		// Authorize signed in users.
		return Auth::check();
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			// User
			'email' => 'required|unique:users,email,' . Auth::id(),

			// Change password
			'current_password' => [
				'nullable', 'string', 'required_with:new_password,new_password_confirmation',
				new PasswordMatch
			],
			'new_password' => 'nullable|required_with:current_password,new_password_confirmation|string|min:6|different:current_password',
			'new_password_confirmation' => 'same:new_password',

			// Editor settings
			'font_size' => 'required|integer',
			'font_family' => [
				'required',
				Rule::in(Config::get('app.fonts')),
			],
			'line_height' => 'required|numeric|between:0,99.99',
		];
	}
}
