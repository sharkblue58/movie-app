<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Actor extends Model
{
    public function movies()
    {
        return $this->morphedByMany(Movie::class, 'castable');
    }
    
    public function series()
    {
        return $this->morphedByMany(Serie::class, 'castable');
    }
}
