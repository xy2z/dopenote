<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Notebook;
use App\Note;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller {
	/*
	|--------------------------------------------------------------------------
	| Register Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles the registration of new users as well as their
	| validation and creation. By default this controller uses a trait to
	| provide this functionality without requiring any additional code.
	|
	*/

	use RegistersUsers;

	/**
	 * Where to redirect users after registration.
	 *
	 * @var string
	 */
	protected $redirectTo = '/';

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->middleware('guest');
	}

	/**
	 * Get a validator for an incoming registration request.
	 *
	 * @param  array  $data
	 * @return \Illuminate\Contracts\Validation\Validator
	 */
	protected function validator(array $data) {
		return Validator::make($data, [
			'name' => ['required', 'string', 'max:255', 'alpha_num', 'unique:users,name'],
			'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
			'password' => ['required', 'string', 'min:6', 'confirmed'],
		]);
	}

	/**
	 * Create a new user instance after a valid registration.
	 *
	 * @param  array  $data
	 * @return \App\User
	 */
	protected function create(array $data) {
		$user = User::create([
			'name' => $data['name'],
			'email' => $data['email'],
			'password' => Hash::make($data['password']),
		]);

		if (!$user) {
			return false;
		}

		self::create_new_user_note($user);

		return $user;
	}

	/**
	 * Create default note and notebook for a new user.
	 *
	 */
	public static function create_new_user_note(User $user) {
		// Create a default notebook
		$notebook = Notebook::create([
			'title' => 'My Notebook',
			'user_id' => $user->id,
			'sort_order' => 0
		]);

		// Create a welcome note
		$note = Note::create([
			'user_id' => $user->id,
			'notebook_id' => $notebook->id,
			'title' => 'Welcome to Dopenote',
			'content' => view('welcome_note'),
			'starred' => true,
		]);
	}
}
