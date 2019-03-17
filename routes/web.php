<?php

Route::get('/', 'NoteController@app')->name('home');
Route::get('/note/{note}', 'NoteController@app');

// Note actions
Route::post('/note/create', 'NoteController@create');
Route::post('/note/{note}/update', 'NoteController@update');
Route::post('/note/{note}/delete', 'NoteController@delete');
Route::post('/note/{note}/toggle_star', 'NoteController@toggle_star');
Route::post('/note/{note}/set_title', 'NoteController@set_title');
Route::post('/note/{note}/set_content', 'NoteController@set_content');

// Notebook actions
Route::post('/notebook/create', 'NotebookController@create');
Route::post('/notebook/update_sort_order', 'NotebookController@update_sort_order');
// - rename
// - delete

// User (todo)
Route::get('/user/settings')->name('user_settings');
Route::get('/user/logout')->name('user_logout');
