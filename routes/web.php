<?php

Route::middleware('auth')->group(function () {
	Route::get('/', 'NoteController@app')->name('home');

	// Note actions
	Route::post('/note/create', 'NoteController@create');

	// Validate user has access to change note.
	Route::middleware(['can:update,note'])->group(function () {
		Route::post('/note/{note}/archive', 'ArchiveNoteController');
		Route::post('/note/{note}/delete', 'NoteController@delete');
		Route::post('/note/{note}/perm_delete', 'NoteController@perm_delete');
		Route::post('/note/{note}/restore', 'NoteController@restore');
		Route::post('/note/{note}/toggle_star', 'NoteController@toggle_star');
		Route::post('/note/{note}/set_title', 'NoteController@set_title');
		Route::post('/note/{note}/set_notebook', 'NoteController@set_notebook');
		Route::post('/note/{note}/set_content', 'NoteController@set_content');
	});


	// Notebook actions
	Route::post('/notebook/create', 'NotebookController@create');
	Route::post('/notebook/update_sort_order', 'NotebookController@update_sort_order');

	// Validate user has access to change notebook.
	Route::middleware(['can:update,notebook'])->group(function () {
		Route::post('/notebook/{notebook}/rename', 'NotebookController@rename');
		Route::post('/notebook/{notebook}/delete', 'NotebookController@delete');
	});


	// User: settings
	Route::get('/user/settings', 'UserSettingsController@show')->name('user_settings');
	Route::get('/user/settings/export', 'UserSettingsController@export')->name('user_settings_export');
	Route::post('/user/settings', 'UserSettingsController@submit');


	// User: Logout
	Route::get('/user/logout', function () {
		Auth::logout();
		return redirect()->route('home');
	})->name('user_logout');
});


// Auth routes for login, register, reset password, etc.
Auth::routes();


// Route bindings
// Make sure $note can be soft deleted.
Route::bind('note', function ($id) {
	return \App\Note::withTrashed()->find($id);
});
