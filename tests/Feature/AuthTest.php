<?php

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can register and receives token', function () {
    $response = $this->postJson('/api/v1/register', [
        'name'                  => 'Test Operator',
        'email'                 => 'operator@test.ci',
        'password'              => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure(['success', 'token', 'user'])
        ->assertJsonPath('success', true);

    expect(User::where('email', 'operator@test.ci')->first()->role)->toBe(UserRole::Operator);
});

test('register always assigns operator role regardless of input', function () {
    $response = $this->postJson('/api/v1/register', [
        'name'                  => 'Sneaky User',
        'email'                 => 'sneaky@test.ci',
        'password'              => 'password',
        'password_confirmation' => 'password',
        'role'                  => 'admin',
    ]);

    $response->assertStatus(201);
    expect(User::where('email', 'sneaky@test.ci')->first()->role)->toBe(UserRole::Operator);
});

test('user can login with valid credentials', function () {
    $user = User::factory()->create(['password' => bcrypt('password')]);

    $response = $this->postJson('/api/v1/login', [
        'email'    => $user->email,
        'password' => 'password',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure(['success', 'token', 'user'])
        ->assertJsonPath('success', true);
});

test('login fails with wrong password', function () {
    $user = User::factory()->create(['password' => bcrypt('password')]);

    $response = $this->postJson('/api/v1/login', [
        'email'    => $user->email,
        'password' => 'wrongpassword',
    ]);

    $response->assertStatus(401)
        ->assertJsonPath('success', false);
});

test('user can logout', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    $response = $this->withToken($token)->postJson('/api/v1/logout');

    $response->assertStatus(200)
        ->assertJsonPath('success', true);
});

test('unauthenticated request returns 401', function () {
    $this->getJson('/api/v1/users')->assertStatus(401);
});
