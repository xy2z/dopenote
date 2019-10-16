<?php

use Illuminate\Database\Seeder;
use App\Http\Controllers\Auth\RegisterController;
use App\User;

class AdminUserSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		$user = User::create([
			'name' => 'Admin',
			'email' => 'admin@localhost',
			'password' => Hash::make('12341234'),
		]);

		RegisterController::create_new_user_note($user);
	}
}
