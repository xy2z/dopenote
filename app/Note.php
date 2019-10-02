<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Note extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'title', 'content', 'starred', 'notebook_id', 'archived_at'];

    protected $casts = [
        'archived_at' => 'datetime',
        'user_id' => 'integer',
    ];

    public function notebook()
    {
        return $this->belongsTo(Notebook::class);
    }

    /**
    *Get the path to the note
    *
    *@return string
    */

    public static function path()
    {
        return "/note/{ note }";
    }
    
    public function archive()
    {
        $this->update([
            'archived_at' => now(),
            'starred' => 0,
        ]);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
