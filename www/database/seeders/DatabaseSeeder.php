<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $categories = Category::factory(5)->create();
        Product::factory(50)->state(function () use ($categories) {
            return [
                'category_id' => mt_rand(1, count($categories)),
                'user_id' => 1
            ];
        })->create();
    }
}
