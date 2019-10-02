<?php

namespace App\Http\Controllers;

use App\Note;
use Illuminate\Http\Request;

class ArchiveNoteController extends Controller
{
    public function __invoke(Note $note)
    {
        $note->archive();
    }
}
