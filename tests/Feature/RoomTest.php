<?php

use App\Models\Room;
use App\Models\User;
use Database\Seeders\RoomSeeder;

describe('room', function () {
    it('can be listed', function () {
        $this->seed(RoomSeeder::class);

        $response = $this
            ->actingAs(User::factory()->create())
            ->getJson('/rooms');

        $response
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    ['user' => []],
                ],
            ])
            ->assertJsonCount(5, 'data');
    });

    it('can be created', function () {
        $this->seed(RoomSeeder::class);
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->postJson('/rooms', [
                'title' => 'Room 6',
            ]);

        $response
            ->assertStatus(201)
            ->assertJson([
                'data' => [
                    'title' => 'Room 6',
                ],
            ]);
    });

    it('can be updated', function () {
        $room = Room::factory()->for(User::factory())->create();
        $user = $room->user;

        $response = $this
            ->actingAs($user)
            ->putJson("/rooms/$room->id", [
                'title' => 'Room 1 Updated',
            ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'title' => 'Room 1 Updated',
                ],
            ]);
    });

    it('cannot be updated by another user', function () {
        $room = Room::factory()->for(User::factory())->create();
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->putJson("/rooms/$room->id", [
                'title' => 'Room 1 Updated',
            ]);

        $response->assertStatus(403);
    });

    it('can be deleted', function () {
        $room = Room::factory()->for(User::factory())->create();
        $user = $room->user;

        $response = $this
            ->actingAs($user)
            ->deleteJson("/rooms/$room->id");

        $response->assertStatus(200);
    });

    it('cannot be deleted by another user', function () {
        $room = Room::factory()->for(User::factory())->create();
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->deleteJson("/rooms/$room->id");

        $response->assertStatus(403);
    });
});
