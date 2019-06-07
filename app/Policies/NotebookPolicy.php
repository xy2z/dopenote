<?php

namespace App\Policies;

use App\User;
use App\Notebook;
use Illuminate\Auth\Access\HandlesAuthorization;

class NotebookPolicy
{
	use HandlesAuthorization;

	/**
	 * Create a new policy instance.
	 *
	 * @return void
	 */
	public function __construct() {
		//
	}

	/**
	 * Validate user can update/delete a notebook
	 *
	 * @param User $user User model
	 * @param Notebook $notebook Notebook model
	 *
	 * @return bool
	 */
	public function update(User $user, Notebook $notebook) {
		return $user->id === $notebook->user_id;
	}
}
