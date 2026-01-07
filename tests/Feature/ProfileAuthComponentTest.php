<?php

namespace Tests\Feature;

use App\Models\User;
use App\Livewire\ProfilePage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

uses(RefreshDatabase::class);

test('new user can register via profile page livewire component', function () {
    Livewire::test(ProfilePage::class)
        ->set('showRegister', true)
        ->set('name', 'Ali Alavi')
        ->set('register_email', 'ali@example.com')
        ->set('register_password', 'Password123')
        ->set('register_password_confirmation', 'Password123')
        ->call('register')
        ->assertRedirect(route('profile'));

    $this->assertDatabaseHas('users', [
        'email' => 'ali@example.com',
        'name' => 'Ali Alavi',
    ]);

    $this->assertAuthenticated();
});

test('existing user can login via profile page livewire component', function () {
    $user = User::factory()->create([
        'email' => 'reza@example.com',
        'password' => Hash::make('Password123'),
    ]);

    Livewire::test(ProfilePage::class)
        ->set('showRegister', false)
        ->set('email', 'reza@example.com')
        ->set('password', 'Password123')
        ->set('remember', true)
        ->call('login')
        ->assertRedirect(route('profile'));

    $this->assertAuthenticatedAs($user);
});

test('login applies remember me cookie by default', function () {
    $user = User::factory()->create([
        'email' => 'remember@example.com',
        'password' => Hash::make('Password123'),
    ]);

    Livewire::test(ProfilePage::class)
        ->set('email', 'remember@example.com')
        ->set('password', 'Password123')
        // 'remember' is true by default now
        ->call('login');

    $this->assertAuthenticatedAs($user);

    // Check if the remember_web cookie or session logic is applied. 
    // In a testing environment, we can verify if the user has a remember_token set.
    $user->refresh();
    $this->assertNotNull($user->remember_token);
});
