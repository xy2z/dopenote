<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Note;
use App\Notebook;

class NoteController extends Controller {

	public function app(Note $note) {
		$notebooks = Notebook::orderBy('sort_order')->get();
		$notes = Note::orderByDesc('id')->get();

		$app_data = [
			'active_notebook_id' => null,
			'active_note_id' => $note->id ?? $notes->first()['id'],
			'waiting_for_ajax' => false,
			'notes' => $notes,
			'notebooks' => $notebooks,
		];

		return view('app', [
			'app_data' => $app_data,
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
		$note->user_id = 0;
		$note->notebook_id = $request->notebook_id;
		$note->save();

		return [
			'result' => true,
			'note' => $note,
		];
	}

	public function delete(Note $note) {
		// TODO: Validate permission.
		$note->delete();

		return [
			'result' => true,
		];
	}

	public function toggle_star(Note $note) {
		$note->starred = !$note->starred;
		$note->save();

		return [
			'result' => true,
			'note' => $note,
		];
	}

	public function set_title(Note $note, Request $request) {
		$note->title = $request->title;
		$note->save();

		return [
			'result' => true,
			'note' => $note,
		];
	}

	public function set_content(Note $note, Request $request) {
		$note->content = $request->content;
		$note->save();

		return [
			'result' => true,
			'note' => $note,
		];
	}

	public function update(Note $note, Request $request) {
		// TODO: Validate access.

		// Save
		$note->title = $request->title;
		$note->content = $request->content;
		$note->save();
	}
}
