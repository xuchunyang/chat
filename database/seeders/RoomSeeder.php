<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 5 rooms with a user
        Room::factory()
            ->count(5)
            ->has(Message::factory()->count(10)->for(User::factory()))
            ->for(User::factory()->state([
                'password' => 'secret_pass_123',
            ]))
            ->create();
    }
}
