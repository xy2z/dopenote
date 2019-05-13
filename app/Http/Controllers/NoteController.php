<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Note;
use App\Notebook;
use App\UserSettings;

/**
 * Note controller
 *
 */
class NoteController extends Controller {

	/**
	 * Constructor
	 *
	 */
	public function __construct() {
		$this->middleware('auth');
	}

	/**
	 * Get data for frontend (vue)
	 *
	 */
	public function app(Note $note) {
		$notebooks = Notebook::where('user_id', Auth::id())->orderBy('sort_order')->get();
		$notes = Note::where('user_id', Auth::id())->withTrashed()->orderByDesc('id')->get();
		$user_settings = UserSettings::get(Auth::id());

		return view('app', [
			'app_data' => [
				'active_notebook_id' => null,
				'active_note_id' => $note->id ?? $notes->first()['id'],
				'waiting_for_ajax' => false,
				'notes' => $notes,
				'notebooks' => $notebooks,
			],
			'user_settings' => $user_settings,
		]);
	}

	/**
	 * Create a new note.
	 *
	 * @param Request $request
	 *
	 * @return array
	 */
	public function create(Request $request) {
		// Todo: Validate $request->notebook_id is mine.

		$note = new Note;
		$note->title = '';
		$note->content = '';
		$note->user_id = Auth::id();
		$note->notebook_id = $request->notebook_id;
		$note->save();

		return [
			'result' => true,
			'note' => $note,
		];
	}

	/**
	 * Delete note (soft delete)
	 *
	 */
	public function delete(Note $note) {
		$note->delete();

		return [
			'result' => true,
			'deleted_at' => date('Y-m-d H:i:s'),
		];
	}

	/**
	 * Permanently delete note (hard delete)
	 *
	 */
	public function perm_delete(Note $note) {
		$note->forceDelete();

		return [
			'result' => true,
		];
	}

	/**
	 * Restore note
	 *
	 */
	public function restore(Note $note) {
		$note->restore();

		return [
			'result' => true,
		];
	}

	/**
	 * Toggle star (favorite) on a note
	 *
	 */
	public function toggle_star(Note $note) {
		$note->starred = !$note->starred;
		$note->save();

		return [
			'result' => true,
			'note' => $note,
		];
	}

	/**
	 * Change title on a note
	 *
	 */
	public function set_title(Note $note, Request $request) {
		$note->title = $request->title ?? '';
		$note->save();

		return [
			'result' => true,
			'note' => $note,
		];
	}

	/**
	 * Update content for a note
	 *
	 */
	public function set_content(Note $note, Request $request) {
		$note->content = $request->content;
		$note->save();

		return [
			'result' => true,
			'note' => $note,
		];
	}

	/**
	 * Update notebook for a note
	 *
	 */
	public function set_notebook(Note $note, Request $request) {
		$note->notebook_id = $request->notebook_id;
		$note->save();

		return [
			'result' => true,
		];
	}

	/**
	 * Update note
	 *
	 */
	public function update(Note $note, Request $request) {
		$note->title = $request->title;
		$note->content = $request->content;
		$note->save();
	}
}
