<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Notebook;
use App\Note;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Socialite;

class LoginController extends Controller
{
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
    public function redirectToProvider($service)
    {
        return Socialite::driver($service)->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback(Request $request, $service)
    {
		//retrieve user details
		try {	$user = Socialite::driver($service)->user();
	} catch (\Exception $e) {
		return redirect('/login')->withErrors(['Please verify your .env keys for ' . $service . ' login.']);
	}


		$email = $user->getEmail();
		$name = $user->getName();

		$user = User::whereEmail($email)->first();

		if (!$user) {
		$user = User::create([
			'name' => $name,
			'email' => $email,
			'password' => Hash::make($email),
		]);
		// TODO: We can convert these two methos into one method and pass parameters
		// in order to create notebook and note records in database.
		// since this code is used twice.
		// once here and once in register controller.

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
			'content' => 'Thanks for trying out <strong>Dopenote</strong>!',
			'starred' => true
		]);
	}


		//second parameter for remembering user
		Auth::login($user, true);

		return redirect('/');
    }
}
