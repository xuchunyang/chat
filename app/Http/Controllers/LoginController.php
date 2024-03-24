<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function authenticate(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'exists:users'],
            'password' => ['required'],
        ]);

        $credentials = $request->only('name', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return new UserResource(Auth::user());
        }

        return response()->json([
            'message' => '密码错误',
            'errors' => [
                'password' => ['密码错误'],
            ],
        ], 401);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return [];
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['nullable', 'email', 'unique:users'],
            'password' => ['required', 'string', 'min:4'],
        ]);

        if (! ($validated['email'] ?? false)) {
            $validated['email'] = Str::uuid().'@example.com';
        }

        $user = User::create($validated);

        Auth::login($user);

        return new UserResource($user);
    }
}
