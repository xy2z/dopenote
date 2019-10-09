<?php

namespace App\Policies;

use App\User;
use App\Note;
use Illuminate\Auth\Access\HandlesAuthorization;

class NotePolicy {
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
	 * Validate user can update/delete a note
	 *
	 * @param User $user User model
	 * @param Note $note Note model
	 *
	 * @return bool
	 */
	public function update(User $user, Note $note) {
		if ($note->archived) {
			return false;
		}
		return $user->id === $note->user_id;
	}
}
