<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Socialite;

class LoginController extends Controller {
	/*
	|--------------------------------------------------------------------------
	| Login Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles authenticating users for the application and
	| redirecting them to your home screen. The controller uses a trait
	| to conveniently provide its functionality to your applications.
	|
	 */

	use AuthenticatesUsers;

	/**
	 * Where to redirect users after login.
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
		$this->middleware('guest')->except('logout');
	}

	/**
	 * Redirect the user to the GitHub authentication page.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function redirectToProvider($service) {
		return Socialite::driver($service)->redirect();
	}

	/**
	 * Obtain the user information from GitHub.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function handleProviderCallback(Request $request, $service) {

		//retrieve user details
		try {
			$user = Socialite::driver($service)->user();
		} catch (\Exception $e) {
			return redirect('/login')->withErrors(['social' => 'Please verify your .env keys for ' . $service . ' login.']);
		}

		$socialAccountId = $user->getId();
		$name = $user->getName();
		$user = User::whereSocialAccountId($socialAccountId)->whereSocialAccountType($service)->first();

		if (!$user) {
			$user = User::create([
				'name' => $name,
				'social_account_id' => $socialAccountId,
				'social_account_type' => $service,
			]);
			RegisterController::create_new_user_note($user);
		}

		//second parameter for remembering user
		Auth::login($user, true);

		return redirect()->route('home');
	}
}
