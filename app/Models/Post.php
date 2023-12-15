<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = array('title', 'content', 'status', 'user_id', 'image', 'video');
    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments() {
        return $this->hasMany(Comment::class);
    }

    public function categories() {
        return $this->belongsToMany(Category::class);
    }
}
