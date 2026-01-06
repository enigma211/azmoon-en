<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use function Pest\Laravel\post;
use function Pest\Laravel\get;

test('user can login with correct credentials', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password123'),
    ]);

    $response = post('/login', [
        'email' => $user->email,
        'password' => 'password123',
    ]);

    $response->assertRedirect('/dashboard'); // or wherever your home is
    $this->assertAuthenticatedAs($user);
});

test('user cannot login with incorrect password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password123'),
    ]);

    $response = post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('new users can register', function () {
    $response = post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'username' => 'testuser',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect('/dashboard'); // or wherever your home is
    $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    $this->assertAuthenticated();
});
