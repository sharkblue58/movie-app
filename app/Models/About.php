<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class About extends Model
{
    protected $fillable = [
        'address',
        'phone',
        'email',
        'fax',
        'zip_code'
    ];
}
