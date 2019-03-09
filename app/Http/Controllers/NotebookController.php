<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Note;
use App\Notebook;

class NotebookController extends Controller {

    public function create() {
    	$user_id = 0; // Todo when auth is done.

    	// Save
		$notebook = new Notebook;
		$notebook->title = 'Notebook';
		$notebook->user_id = $user_id;
		$notebook->sort_order = Notebook::get_last_sort_order($user_id) + 1;
		$notebook->save();

		return [
			'result' => true,
			'notebook' => $notebook,
		];
    }

}
