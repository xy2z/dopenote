<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Note;
use App\Notebook;

class NotebookController extends Controller {

    public function create() {

		$notebook = new Notebook;
		$notebook->title = 'Notebook';
		$notebook->user_id = 0;
		$notebook->sort_order = 1; // Todo: Set notebook to latest 'sort_order' of this user.
		$notebook->save();

		return [
			'result' => true,
			'notebook' => $notebook,
		];
    }
}
