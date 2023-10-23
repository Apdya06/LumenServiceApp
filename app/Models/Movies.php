<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movies extends Model{
    protected $fillable = array('title', 'genre', 'duration', 'rated', 'producer', 'studio', 'rating');
}
