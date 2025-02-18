<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * @OA\Schema(
 *     schema="Category",
 *     type="object",
 *     title="Category",
 *     description="Category model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Technology"),
 *     @OA\Property(property="description", type="string", example="Technology is best way to change the world"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-02-18T12:34:56Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-02-18T12:34:56Z")
 * )
 */
class Category extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function movies()
    {
        return $this->belongsToMany(Movie::class,'movie_serie_category');
    }

    public function series()
    {
        return $this->belongsToMany(Serie::class,'movie_serie_category');
    }
}
