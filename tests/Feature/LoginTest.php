<?php

use App\Models\User;

describe('Login', function () {
    test('can be authenticated', function () {
        $r0 = $this->getJson('/sanctum/csrf-cookie');

        $cookies = $r0->headers->all('Set-Cookie');

        // ray(explode('=', explode(';', $cookies[0])[0]));

        $cookiesPairs = array_reduce($cookies, function ($acc, $cookie) {
            $pair = explode('=', explode(';', $cookie)[0]);
            // ray($pair);
            $acc[$pair[0]] = $pair[1];

            return $acc;
        }, []);

        // ray($cookies, $cookiesPairs);

        $user = User::factory()->create([
            'email' => 'hello@example.com',
            'password' => 'top-secret',
        ]);

        $response = $this->withCookies($cookiesPairs)->postJson(route('login'), [
            'email' => 'hello@example.com',
            'password' => 'top-secret',
        ]);

        $response->dump('message');

        $response
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $user->id,
                ],
            ]);
    });
})->skip('do not know how to test stateful login/logout yet');
