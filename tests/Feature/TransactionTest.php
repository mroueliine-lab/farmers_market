<?php

use App\Models\User;
use App\Models\Farmer;
use App\Models\Product;
use App\Models\Category;
use App\Models\Setting;
use App\Models\Debt;
use App\Enums\UserRole;
use App\Enums\DebtStatus;
use App\Enums\PaymentMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    Setting::create(['key' => 'interest_rate',  'value' => '30']);
    Setting::create(['key' => 'commodity_rate', 'value' => '1000']);

    $this->operator = User::factory()->create(['role' => UserRole::Operator]);
    $this->token    = $this->operator->createToken('test')->plainTextToken;

    $category       = Category::factory()->create();
    $this->product  = Product::factory()->create(['price_fcfa' => 10000, 'category_id' => $category->id]);
    $this->farmer   = Farmer::factory()->create(['credit_limit' => 100000]);
});

// ── Cash transaction ──────────────────────────────────────────────────────────

test('operator can create a cash transaction', function () {
    $response = $this->withToken($this->token)->postJson('/api/v1/transactions', [
        'farmer_id'      => $this->farmer->id,
        'payment_method' => 'cash',
        'items'          => [['product_id' => $this->product->id, 'quantity' => 2]],
    ]);

    $response->assertStatus(201)
        ->assertJsonPath('data.payment_method', 'cash')
        ->assertJsonPath('data.total_price_fcfa', '20000.00')
        ->assertJsonPath('data.interest_rate', '0.00')
        ->assertJsonPath('data.credited_amount_fcfa', '0.00');

    expect(Debt::count())->toBe(0);
});

test('cash transaction does not create a debt', function () {
    $this->withToken($this->token)->postJson('/api/v1/transactions', [
        'farmer_id'      => $this->farmer->id,
        'payment_method' => 'cash',
        'items'          => [['product_id' => $this->product->id, 'quantity' => 1]],
    ]);

    expect(Debt::count())->toBe(0);
});

// ── Credit transaction ────────────────────────────────────────────────────────

test('operator can create a credit transaction with interest applied', function () {
    $response = $this->withToken($this->token)->postJson('/api/v1/transactions', [
        'farmer_id'      => $this->farmer->id,
        'payment_method' => 'credit',
        'items'          => [['product_id' => $this->product->id, 'quantity' => 2]],
    ]);

    // total = 20000, credited = 20000 * 1.30 = 26000
    $response->assertStatus(201)
        ->assertJsonPath('data.total_price_fcfa', '20000.00')
        ->assertJsonPath('data.interest_rate', '30.00')
        ->assertJsonPath('data.credited_amount_fcfa', '26000.00');
});

test('credit transaction creates a pending debt', function () {
    $this->withToken($this->token)->postJson('/api/v1/transactions', [
        'farmer_id'      => $this->farmer->id,
        'payment_method' => 'credit',
        'items'          => [['product_id' => $this->product->id, 'quantity' => 1]],
    ]);

    $debt = Debt::first();
    expect($debt)->not->toBeNull()
        ->and($debt->status)->toBe(DebtStatus::Pending)
        ->and((float) $debt->original_amount_fcfa)->toBe(13000.0)
        ->and((float) $debt->remaining_amount_fcfa)->toBe(13000.0);
});

// ── Credit limit enforcement ──────────────────────────────────────────────────

test('credit transaction is blocked when credit limit would be exceeded', function () {
    $this->farmer->update(['credit_limit' => 5000]);

    $response = $this->withToken($this->token)->postJson('/api/v1/transactions', [
        'farmer_id'      => $this->farmer->id,
        'payment_method' => 'credit',
        'items'          => [['product_id' => $this->product->id, 'quantity' => 1]],
    ]);

    $response->assertStatus(422)
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Credit limit exceeded');

    expect(Debt::count())->toBe(0);
});

test('credit limit accounts for existing outstanding debts', function () {
    // Create existing debt of 80000
    Debt::factory()->create([
        'farmer_id'            => $this->farmer->id,
        'original_amount_fcfa'  => 80000,
        'remaining_amount_fcfa' => 80000,
        'status'               => DebtStatus::Pending,
    ]);

    // New transaction would add 13000 (10000 * 1.30) → total 93000 < 100000 limit → OK
    $this->withToken($this->token)->postJson('/api/v1/transactions', [
        'farmer_id'      => $this->farmer->id,
        'payment_method' => 'credit',
        'items'          => [['product_id' => $this->product->id, 'quantity' => 1]],
    ])->assertStatus(201);

    // Add another that would push it over (13000 more → 106000 > 100000) → blocked
    $this->withToken($this->token)->postJson('/api/v1/transactions', [
        'farmer_id'      => $this->farmer->id,
        'payment_method' => 'credit',
        'items'          => [['product_id' => $this->product->id, 'quantity' => 1]],
    ])->assertStatus(422);
});

// ── Transaction show ──────────────────────────────────────────────────────────

test('can retrieve transaction with items, farmer and debt', function () {
    $response = $this->withToken($this->token)->postJson('/api/v1/transactions', [
        'farmer_id'      => $this->farmer->id,
        'payment_method' => 'credit',
        'items'          => [['product_id' => $this->product->id, 'quantity' => 1]],
    ]);

    $id = $response->json('data.id');

    $this->withToken($this->token)->getJson("/api/v1/transactions/{$id}")
        ->assertStatus(200)
        ->assertJsonStructure(['data' => ['items', 'farmer', 'debt']]);
});

// ── Validation ────────────────────────────────────────────────────────────────

test('transaction requires at least one item', function () {
    $this->withToken($this->token)->postJson('/api/v1/transactions', [
        'farmer_id'      => $this->farmer->id,
        'payment_method' => 'cash',
        'items'          => [],
    ])->assertStatus(422);
});

test('transaction rejects invalid payment method', function () {
    $this->withToken($this->token)->postJson('/api/v1/transactions', [
        'farmer_id'      => $this->farmer->id,
        'payment_method' => 'barter',
        'items'          => [['product_id' => $this->product->id, 'quantity' => 1]],
    ])->assertStatus(422);
});
