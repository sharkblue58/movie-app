<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    protected $fillable = [
        'title', 'release_date', 'description', 'rating', 'duration', 'poster_url', 'category_id'
    ];
    public function categories()
    {
        return $this->belongsToMany(Category::class,'movie_serie_category');
    }

    public function actors()
    {
        return $this->belongsToMany(Actor::class, 'cast');
    }
}
