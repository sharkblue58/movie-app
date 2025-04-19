<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Episode extends Model
{
    public function season()
    {
        return $this->belongsTo(Season::class);
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
