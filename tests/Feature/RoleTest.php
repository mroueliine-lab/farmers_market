<?php

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function makeUser(UserRole $role): User
{
    return User::factory()->create(['role' => $role]);
}

function actingAs(UserRole $role)
{
    $user = makeUser($role);
    $token = $user->createToken('test')->plainTextToken;
    return test()->withToken($token);
}

// ── Users (admin only) ───────────────────────────────────────────────────────

test('admin can list users', function () {
    actingAs(UserRole::Admin)->getJson('/api/v1/users')->assertStatus(200);
});

test('supervisor cannot access users endpoint', function () {
    actingAs(UserRole::Supervisor)->getJson('/api/v1/users')->assertStatus(403);
});

test('operator cannot access users endpoint', function () {
    actingAs(UserRole::Operator)->getJson('/api/v1/users')->assertStatus(403);
});

// ── Operators (supervisor only) ──────────────────────────────────────────────

test('supervisor can list operators', function () {
    actingAs(UserRole::Supervisor)->getJson('/api/v1/operators')->assertStatus(200);
});

test('admin cannot access operators endpoint', function () {
    actingAs(UserRole::Admin)->getJson('/api/v1/operators')->assertStatus(403);
});

test('operator cannot access operators endpoint', function () {
    actingAs(UserRole::Operator)->getJson('/api/v1/operators')->assertStatus(403);
});

// ── Categories (admin & supervisor) ─────────────────────────────────────────

test('admin can create a category', function () {
    actingAs(UserRole::Admin)->postJson('/api/v1/categories', [
        'name' => 'Pesticides',
    ])->assertStatus(201);
});

test('supervisor can create a category', function () {
    actingAs(UserRole::Supervisor)->postJson('/api/v1/categories', [
        'name' => 'Engrais',
    ])->assertStatus(201);
});

test('operator cannot create a category', function () {
    actingAs(UserRole::Operator)->postJson('/api/v1/categories', [
        'name' => 'Semences',
    ])->assertStatus(403);
});

// ── Transactions (operator only creates) ─────────────────────────────────────

test('admin cannot create a transaction', function () {
    $farmer = \App\Models\Farmer::factory()->create();
    $category = \App\Models\Category::factory()->create();
    $product = \App\Models\Product::factory()->create(['category_id' => $category->id]);

    actingAs(UserRole::Admin)->postJson('/api/v1/transactions', [
        'farmer_id'      => $farmer->id,
        'payment_method' => 'cash',
        'items'          => [['product_id' => $product->id, 'quantity' => 1]],
    ])->assertStatus(403);
});
