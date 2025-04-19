<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['user_id','is_light_mode','is_full_screen','is_notifiable'];
    protected $casts = [
        'is_light_mode' => 'boolean',
        'is_full_screen' => 'boolean',
        'is_notifiable' => 'boolean',
    ];
    public function user(){
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
