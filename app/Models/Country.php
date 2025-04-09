<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'name',
        'code',
    ];

    public function movies()
    {
        return $this->hasMany(Movie::class);
    }

    public function series ()
    {
        return $this->hasMany(Serie::class);
    }
}
