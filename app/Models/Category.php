<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = array('title', 'content', 'status', 'user_id');
    public $timestamps = true;

    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }
}
