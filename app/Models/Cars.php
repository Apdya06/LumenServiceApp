<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cars extends Model{
    protected $fillable = array('brand', 'name', 'transmission', 'horsepower', 'dimensions', 'fuel_capacity', 'engine');
}
