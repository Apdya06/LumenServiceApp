<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cpus extends Model{
    protected $table = 'cpus';
    protected $fillable = array('brand', 'name');
}
