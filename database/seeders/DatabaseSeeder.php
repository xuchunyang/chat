<?php

namespace Database\Seeders;

use App\Models\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // create kimi user
        User::factory()->create([
            'name' => 'kimi',
            'email' => Str::uuid() . '@example.com',
            'password' => Str::uuid(),
        ]);

        User::factory()->create([
            'name' => 'test-user',
            'email' => 'test@example.com',
        ]);

        $this->call([
            RoomSeeder::class,
        ]);
    }
}
