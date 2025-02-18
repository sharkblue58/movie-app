<?php

namespace App\Models;

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
}
