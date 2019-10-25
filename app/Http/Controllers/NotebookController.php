<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Note;
use App\Notebook;

/**
 * Notebook controller
 */
class NotebookController extends Controller {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->middleware('auth');
	}

	/**
	 * Create new notebook
	 *
	 */
	public function create() {
		$notebook = new Notebook;
		$notebook->title = 'Notebook';
		$notebook->user_id = Auth::id();
		$notebook->sort_order = Notebook::get_last_sort_order(Auth::id()) + 1;
		$notebook->save();

		return [
			'result' => true,
			'notebook' => $notebook,
		];
	}

	/**
	 * Change notebooks sort order
	 *
	 */
	public function update_sort_order(request $request) {
		$old_index = (int) $request->old_index;
		$new_index = (int) $request->new_index;

		// Get all notebooks sorted by index
		$notebooks = Notebook::where('user_id', Auth::id())->orderBy('sort_order')->get();

		// Sort all notebooks
		// Array: $sort_order => $notebook_id
		$sorted_ids = [];
		foreach ($notebooks as $key => $notebook) {
			$sorted_ids[] = $notebook->id;
		}

		// Remove old index
		$notebook_id = $sorted_ids[$old_index];
		unset($sorted_ids[$old_index]);

		// Add new index
		array_splice($sorted_ids, $new_index, 0, $notebook_id); // splice in at position 3

		// Update all id's with their new index
		foreach ($sorted_ids as $index => $id) {
			Notebook::where('id', $id)->update([
				'sort_order' => $index
			]);
		}
	}

	/**
	 * Perm. delete notebook
	 *
	 */
	public function delete(Notebook $notebook, request $request) {
		$deleted_notes = $notebook->notes()->pluck('id');

		// Soft-delete all notes in this notebook.
		Note::whereIn('id', $notebook->notes->pluck('id'))->delete();

		// Delete this notebook.
		$notebook->delete();

		// Response
		return [
			'notes' => $deleted_notes,
			'deleted_at' => date('Y-m-d H:i:s'),
		];
	}

	/**
	 * Rename notebook title
	 *
	 */
	public function rename(Notebook $notebook, request $request) {
		$notebook->title = $request->title;
		$notebook->save();
	}
}
