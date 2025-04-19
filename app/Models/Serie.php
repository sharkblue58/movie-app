<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Serie extends Model
{
    use HasFactory ;
    protected $fillable = [
        'title', 'release_date', 'description', 'rating', 'duration', 'poster_url','country_id','studio_id'
    ];
    public function categories()
    {
        return $this->morphToMany(Category::class, 'categoryable');
    }

    public function actors()
    {
        return $this->morphToMany(Actor::class, 'castable');
    }

    public function seasons()
    {
        return $this->hasMany(Season::class);
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

    public function studio(){
        return $this->belongsTo(Studio::class);
    }

    public function getCreatedAtAttribute($value)
{
    return Carbon::parse($value)->format('Y-m-d H:i:s');
}

public function getUpdatedAtAttribute($value)
{
    return Carbon::parse($value)->format('Y-m-d H:i:s');
}
}
