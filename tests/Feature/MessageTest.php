<?php

use App\Models\Room;
use App\Models\User;
use Database\Seeders\RoomSeeder;

describe('message', function () {
    test('can be listed', function () {
        $this->seed(RoomSeeder::class);
        $room = Room::first();
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->getJson(route('rooms.messages.index', $room));

        $response
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    ['user' => []],
                ],
            ])
            ->assertJsonCount(10, 'data');
    });

    test('can be created', function () {
        $this->seed(RoomSeeder::class);
        $room = Room::first();
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->postJson(route('rooms.messages.store', $room), [
                'content' => 'Hello, World!',
            ]);

        // $response->dump('message');

        $response
            ->assertStatus(201)
            ->assertJson([
                'data' => [
                    'content' => 'Hello, World!',
                ],
            ]);
    });

    test('can be updated', function () {
        $this->seed(RoomSeeder::class);
        $room = Room::first();
        $message = $room->messages()->first();
        $user = $message->user;

        $response = $this
            ->actingAs($user)
            ->putJson(route('messages.update', $message), [
                'content' => 'Hello, World! Updated',
            ]);

        // $response->dump('message');

        $response
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'content' => 'Hello, World! Updated',
                ],
            ]);
    });

    test('cannot be updated by another user', function () {
        $this->seed(RoomSeeder::class);
        $room = Room::first();
        $message = $room->messages()->first();
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->putJson(route('messages.update', $message), [
                'content' => 'Hello, World! Updated',
            ]);

        // $response->dump();

        $response->assertStatus(403);
    });

    test('can be deleted', function () {
        $this->seed(RoomSeeder::class);
        $room = Room::first();
        $message = $room->messages()->first();
        $user = $message->user;

        $response = $this
            ->actingAs($user)
            ->deleteJson(route('messages.destroy', $message));

        $response->assertStatus(200);
    });

    test('cannot be deleted by another user', function () {
        $this->seed(RoomSeeder::class);
        $room = Room::first();
        $message = $room->messages()->first();
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->deleteJson(route('messages.destroy', $message));

        $response->assertStatus(403);
    });
});
