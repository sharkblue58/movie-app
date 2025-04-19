<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{

    protected $fillable = [
        'user_id',
        'subject',
        'message',
        'status',
        'type',
        'attachment'
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
