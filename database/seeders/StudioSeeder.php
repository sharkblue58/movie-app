<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Studio;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class StudioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $studios = [
            'Warner Bros. Pictures',
            'Walt Disney Studios',
            'Universal Pictures',
            'Paramount Pictures',
            '20th Century Studios',
            'Columbia Pictures',
            'Netflix Studios',
            'HBO',
            'Amazon MGM Studios',
            'Lionsgate',
        ];

        foreach ($studios as $name) {
            Studio::create([
                'name' => $name,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
