<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Note extends Model {
	use SoftDeletes;

	protected $fillable = ['user_id', 'title', 'content', 'starred', 'notebook_id'];

	public function notebook() {
		return $this->belongsTo(Notebook::class);
	}

	/**
	*Get the path to the note
	*
	*@return string
	*/

	public static function path(){
		return "/note/{ note }";
	}
}
