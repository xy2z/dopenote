<?php

Route::get('/', 'NoteController@app')->name('home');


// Note actions
Route::post('/note/create', 'NoteController@create');
// Validate user has access to change note.
Route::middleware(['can:update,note'])->group(function () {
	Route::post('/note/{note}/delete', 'NoteController@delete');
	Route::post('/note/{note}/toggle_star', 'NoteController@toggle_star');
	Route::post('/note/{note}/set_title', 'NoteController@set_title');
	Route::post('/note/{note}/set_content', 'NoteController@set_content');
});


// Notebook actions
Route::post('/notebook/create', 'NotebookController@create');
// Validate user has access to change notebook.
Route::middleware(['can:update,notebook'])->group(function () {
	Route::post('/notebook/update_sort_order', 'NotebookController@update_sort_order');
	Route::post('/notebook/{notebook}/rename', 'NotebookController@rename');
	Route::post('/notebook/{notebook}/delete', 'NotebookController@delete');
});


// User
Route::get('/user/settings')->name('user_settings');
Route::get('/user/logout', function() {
	Auth::logout();
	return redirect()->route('home');
})->name('user_logout');


// Auth routes for login, register, reset password, etc.
Auth::routes();
