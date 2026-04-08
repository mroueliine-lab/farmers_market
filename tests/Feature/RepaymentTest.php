<?php

use App\Models\User;
use App\Models\Farmer;
use App\Models\Setting;
use App\Models\Debt;
use App\Models\Transaction;
use App\Enums\UserRole;
use App\Enums\DebtStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    Setting::create(['key' => 'interest_rate',  'value' => '30']);
    Setting::create(['key' => 'commodity_rate', 'value' => '1000']);

    $this->operator = User::factory()->create(['role' => UserRole::Operator]);
    $this->token    = $this->operator->createToken('test')->plainTextToken;
    $this->farmer   = Farmer::factory()->create(['credit_limit' => 500000]);
});

function createDebt(Farmer $farmer, float $amount, DebtStatus $status = DebtStatus::Pending): Debt
{
    $tx = Transaction::factory()->create([
        'farmer_id'  => $farmer->id,
        'operator_id' => User::factory()->create(['role' => UserRole::Operator])->id,
    ]);

    return Debt::factory()->create([
        'farmer_id'             => $farmer->id,
        'transaction_id'        => $tx->id,
        'original_amount_fcfa'  => $amount,
        'remaining_amount_fcfa' => $amount,
        'status'                => $status,
    ]);
}

// ── Basic repayment ───────────────────────────────────────────────────────────

test('operator can record a repayment', function () {
    createDebt($this->farmer, 50000);

    $response = $this->withToken($this->token)->postJson('/api/v1/repayments', [
        'farmer_id'   => $this->farmer->id,
        'kg_received' => 20,
    ]);

    $response->assertStatus(201)
        ->assertJsonPath('data.fcfa_value', '20000.00')
        ->assertJsonPath('data.commodity_rate', '1000.00');
});

// ── Full settlement ───────────────────────────────────────────────────────────

test('debt is marked paid when repayment covers full amount', function () {
    $debt = createDebt($this->farmer, 10000);

    $this->withToken($this->token)->postJson('/api/v1/repayments', [
        'farmer_id'   => $this->farmer->id,
        'kg_received' => 10, // 10 * 1000 = 10000 FCFA
    ]);

    $debt->refresh();
    expect($debt->status)->toBe(DebtStatus::Paid)
        ->and((float) $debt->remaining_amount_fcfa)->toBe(0.0);
});

// ── Partial repayment ─────────────────────────────────────────────────────────

test('debt is marked partial when repayment is less than amount', function () {
    $debt = createDebt($this->farmer, 20000);

    $this->withToken($this->token)->postJson('/api/v1/repayments', [
        'farmer_id'   => $this->farmer->id,
        'kg_received' => 5, // 5 * 1000 = 5000 FCFA
    ]);

    $debt->refresh();
    expect($debt->status)->toBe(DebtStatus::Partial)
        ->and((float) $debt->remaining_amount_fcfa)->toBe(15000.0);
});

// ── FIFO ─────────────────────────────────────────────────────────────────────

test('FIFO: oldest debt is settled first', function () {
    $oldest = createDebt($this->farmer, 10000);
    sleep(1); // ensure different timestamps
    $newest = createDebt($this->farmer, 10000);

    $this->withToken($this->token)->postJson('/api/v1/repayments', [
        'farmer_id'   => $this->farmer->id,
        'kg_received' => 10, // exactly covers oldest debt
    ]);

    $oldest->refresh();
    $newest->refresh();

    expect($oldest->status)->toBe(DebtStatus::Paid)
        ->and($newest->status)->toBe(DebtStatus::Pending);
});

test('FIFO: repayment spans multiple debts', function () {
    $debt1 = createDebt($this->farmer, 10000);
    sleep(1);
    $debt2 = createDebt($this->farmer, 10000);

    // 15kg * 1000 = 15000 FCFA → covers debt1 (10000) fully + debt2 (5000) partially
    $this->withToken($this->token)->postJson('/api/v1/repayments', [
        'farmer_id'   => $this->farmer->id,
        'kg_received' => 15,
    ]);

    $debt1->refresh();
    $debt2->refresh();

    expect($debt1->status)->toBe(DebtStatus::Paid)
        ->and((float) $debt1->remaining_amount_fcfa)->toBe(0.0)
        ->and($debt2->status)->toBe(DebtStatus::Partial)
        ->and((float) $debt2->remaining_amount_fcfa)->toBe(5000.0);
});

// ── repayment_id linked ───────────────────────────────────────────────────────

test('settled debts are linked to the repayment record', function () {
    $debt = createDebt($this->farmer, 5000);

    $response = $this->withToken($this->token)->postJson('/api/v1/repayments', [
        'farmer_id'   => $this->farmer->id,
        'kg_received' => 5,
    ]);

    $debt->refresh();
    expect($debt->repayment_id)->toBe($response->json('data.id'));
});

// ── Validation ────────────────────────────────────────────────────────────────

test('repayment requires kg_received greater than zero', function () {
    $this->withToken($this->token)->postJson('/api/v1/repayments', [
        'farmer_id'   => $this->farmer->id,
        'kg_received' => 0,
    ])->assertStatus(422);
});
