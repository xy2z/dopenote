<?php

Route::get('/', 'NoteController@app')->name('home');
Route::get('/note/{note}', 'NoteController@app');

// Actions (post)
Route::post('/note/create', 'NoteController@create');
Route::post('/note/{note}/update', 'NoteController@update');
Route::post('/note/{note}/delete', 'NoteController@delete');
Route::post('/note/{note}/toggle_star', 'NoteController@toggle_star');
Route::post('/note/{note}/set_title', 'NoteController@set_title');
Route::post('/note/{note}/set_content', 'NoteController@set_content');
