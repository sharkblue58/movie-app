<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorties = [
            ['name' => 'Action', 'description' => 'High-energy films with intense physical stunts, chases, fights, and explosions. Often features heroes battling villains.','created_at' => now()],
            ['name' => 'Adventure', 'description' => 'Movies that involve exciting journeys, explorations, and quests, often in exotic or fantastical locations.','created_at' => now()],
            ['name' => 'Comedy', 'description' => 'Lighthearted films designed to entertain and amuse, often through humor, jokes, and funny situations.','created_at' => now()],
            ['name' => 'Drama', 'description' => 'Serious, emotional stories that focus on character development and real-life situations.','created_at' => now()],
            ['name' => 'Horror', 'description' => 'Films intended to scare, shock, or unsettle the audience, often featuring supernatural elements, monsters, or psychological terror.','created_at' => now()],
            ['name' => 'Science Fiction', 'description' => 'Movies that explore futuristic concepts, advanced technology, space exploration, and extraterrestrial life.','created_at' => now()],
            ['name' => 'Fantasy', 'description' => 'Films set in imaginary worlds with magical elements, mythical creatures, and epic adventures.','created_at' => now()],
            ['name' => 'Romance', 'description' => 'Stories centered around love and relationships, often with emotional and heartfelt moments.','created_at' => now()],
            ['name' => 'Thriller', 'description' => 'Suspenseful films that keep the audience on the edge of their seats, often involving crime, mystery, or danger.','created_at' => now()],
            ['name' => 'Mystery', 'description' => 'Movies that involve solving a puzzle, crime, or enigmatic situation, often with a twist ending.','created_at' => now()],
            ['name' => 'Animation', 'description' => 'Films created using animated techniques, often targeting children but also enjoyed by adults.','created_at' => now()],
            ['name' => 'Family', 'description' => 'Movies suitable for all ages, often with positive messages and themes.','created_at' => now()],
            ['name' => 'Documentary', 'description' => 'Non-fiction films that document reality, often focusing on real-life events, people, or issues.','created_at' => now()],
            ['name' => 'Musical', 'description' => 'Films that incorporate songs and dance numbers as part of the narrative.','created_at' => now()],
            ['name' => 'Historical', 'description' => 'Movies based on real historical events, figures, or periods.','created_at' => now()],
            ['name' => 'War', 'description' => 'Films that focus on warfare, battles, and the impact of war on individuals and societies.','created_at' => now()],
            ['name' => 'Western', 'description' => 'Movies set in the American Old West, often featuring cowboys, outlaws, and frontier life.','created_at' => now()],
            ['name' => 'Crime', 'description' => 'Films centered around criminal activities, heists, and the justice system.','created_at' => now()],
            ['name' => 'Superhero', 'description' => 'Movies featuring superheroes with extraordinary abilities who fight evil and protect the world.','created_at' => now()],
            ['name' => 'Sports', 'description' => 'Films that revolve around sports, athletes, and competitions, often with inspirational themes.','created_at' => now()],
            ['name' => 'Biographical', 'description' => 'Movies based on the life of a real person, often highlighting their achievements or struggles.','created_at' => now()],
            ['name' => 'Musical', 'description' => ' Films that incorporate songs and dance numbers as part of the narrative.','created_at' => now()],
            ['name' => 'Art House', 'description' => 'Independent films with a focus on artistic expression, often experimental or unconventional.','created_at' => now()],
            ['name' => 'Cult', 'description' => 'Movies that have developed a dedicated fanbase over time, often due to their unique or unconventional style.','created_at' => now()],
            ['name' => 'Foreign', 'description' => 'Films produced outside of the viewer’s country, often in a different language.','created_at' => now()],
        ];
        Category::insert($categorties);
    }
}
