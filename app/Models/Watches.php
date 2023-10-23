<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Watches extends Model{
    protected $fillable = array('brand', 'name', 'production_year', 'material', 'weight', 'dimensions', 'waterproof');
}
