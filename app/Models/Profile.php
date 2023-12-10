<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = array('user_id', 'first_name', 'last_name', 'summary', 'image');
    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
