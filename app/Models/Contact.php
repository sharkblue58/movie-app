<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{

    protected $fillable = [
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
}
