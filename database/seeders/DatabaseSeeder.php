<?php

namespace Database\Seeders;

use App\Models\Board;
use App\Models\Post;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $random = Board::create([
            'route' => 'b',
            'name' => 'Random',
            'description' => 'Random',
        ]);

        $technology = Board::create([
            'route' => 'g',
            'name' => 'Technology',
            'description' => 'Technology',
        ]);

        Post::factory()
            ->thread()
            ->count(20)
            ->for($random)
            ->has(
                factory: Post::factory()
                    ->reply()
                    ->for($random)
                    ->count(5),
                relationship: 'replies'
            )
            ->create();

        Post::factory()
            ->thread()
            ->count(20)
            ->for($technology)
            ->has(
                factory: Post::factory()
                    ->reply()
                    ->for($technology)
                    ->count(5),
                relationship: 'replies'
            )
            ->create();
    }
}
