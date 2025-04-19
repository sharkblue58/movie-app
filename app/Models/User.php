<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Carbon\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{

    use HasFactory, Notifiable, HasRoles, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'profile_image',
        'email',
        'password',
        'phone',
        'birth_date',
        'gender',
        'address',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function SocialEmail()
    {
        return $this->hasMany(SocialEmail::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function setting()
    {
        return $this->hasOne(Setting::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function watchedMovies()
    {
        return $this->morphedByMany(Movie::class, 'watchable', 'watch_histories')->withPivot('watch_date')
        ->withTimestamps();
    }

    public function watchedSeries()
    {
        return $this->morphedByMany(Serie::class, 'watchable', 'watch_histories')->withPivot('watch_date')
        ->withTimestamps();
    }

    public function watchedLaterMovies()
    {
        return $this->morphedByMany(Movie::class, 'watchable', 'watch_laters');
    }

    public function watchedLaterSeries()
    {
        return $this->morphedByMany(Serie::class, 'watchable', 'watch_laters');
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
