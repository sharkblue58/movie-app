<?php

namespace App\Models;

use Carbon\Carbon;
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

    public function getCreatedAtAttribute($value)
{
    return Carbon::parse($value)->format('Y-m-d H:i:s');
}

public function getUpdatedAtAttribute($value)
{
    return Carbon::parse($value)->format('Y-m-d H:i:s');
}
}
