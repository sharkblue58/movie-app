<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    protected $fillable = [
        'title', 'release_date', 'description', 'rating', 'duration', 'poster_url','country_id'
    ];
    public function categories()
    {
        return $this->morphToMany(Category::class, 'categoryable');
    }

    public function actors()
    {
        return $this->morphToMany(Actor::class, 'castable');
    }

    public function country(){
        return $this->belongsTo(Country::class);
    }

    public function watchedAgainBy()
    {
        return $this->morphedByMany(User::class, 'watchable', 'watch_histories');
    }

    public function watchedLaterBy()
    {
        return $this->morphedByMany(User::class, 'watchable', 'watch_laters');
    }
}
