<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'body', 'user_id', 'publication_id'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function publication()
    {
        return $this->belongsTo('App\Publication');
    }
}
