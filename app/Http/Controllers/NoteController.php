<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Note;

class NoteController extends Controller
{
    public function app(Note $note)
    {
    	$notes = Note::orderByDesc('id')->get();

    	return view('app', compact('notes', 'note'));
    }

    public function create()
    {
    	$note = new Note;
    	$note->title = 'New note';
    	$note->content = 'Hello World';
    	$note->user_id = 0;
    	$note->save();

    	return redirect('/note/' . $note->id);
    }
}
