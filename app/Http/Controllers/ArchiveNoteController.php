<?php

namespace App\Http\Controllers;

use App\Note;

class ArchiveNoteController extends Controller {
	public function __invoke(Note $note) {
		$note->archive();
	}
}
