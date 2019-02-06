<?php

Route::get('/', 'NoteController@app')->name('home');
Route::get('/note/{note}', 'NoteController@app');
Route::post('/note/create', 'NoteController@create');
