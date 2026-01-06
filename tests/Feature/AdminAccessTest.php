<?php

namespace Tests\Feature;

use App\Models\User;
use Spatie\Permission\Models\Role;
use function Pest\Laravel\actingAs;

test('guests are redirected from admin dashboard to login', function () {
    $this->get('/admin')
        ->assertRedirect('/admin/login');
});

test('non-admin users are forbidden from admin dashboard', function () {
    // Create a normal user (no roles)
    $user = User::factory()->create();

    actingAs($user)
        ->get('/admin')
        ->assertStatus(403);
});

test('admin users can access admin dashboard', function () {
    // We need to setup Spatie roles for this to work
    // In a test environment, we might need to manually create the role
    $adminRole = Role::create(['name' => 'admin']);

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    actingAs($admin)
        ->get('/admin')
        ->assertStatus(200);
});
