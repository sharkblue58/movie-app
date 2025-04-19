<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class SocialEmail extends Model
{
    protected $fillable=[
      'provider',
      'provider_id',
      'avatar',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
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
