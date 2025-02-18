<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Serie extends Model
{
    public function categories()
    {
        return $this->belongsToMany(Category::class,'movie_serie_category');
    }

    public function actors()
    {
        return $this->belongsToMany(Actor::class, 'cast');
    }

    public function seasons()
    {
        return $this->hasMany(Season::class);
    }
}
