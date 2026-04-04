<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Debt;
use App\Models\TransactionItem;
use App\Models\Farmer;

class TransactionController extends Controller
{
public function store(Request $request)
    {
        $validated = $request->validate([
            'farmer_id'          => 'required|exists:farmers,id',
            'payment_method'     => 'required|in:cash,credit',
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
        ]);

$productIds = collect($validated['items'])->pluck('product_id');
$products = Product::whereIn('id', $productIds)->get()->keyBy('id');
$total = 0;
foreach ($validated['items'] as $item) {
    $product = $products[$item['product_id']];
    $total += $product->price_fcfa * $item['quantity'];
}

$interestRate = 0;
$creditedAmount = 0;


if ($validated['payment_method'] === 'credit') {
    
    $interestRate = Setting::where('key', 'interest_rate')->value('value') ?? 0;
    $creditedAmount = $total * (1 + $interestRate / 100);
    $farmer = Farmer::findOrFail($validated['farmer_id']);

    $existingDebt = $farmer->debts()
        ->whereIn('status', ['pending', 'partial'])
        ->sum('remaining_amount_fcfa');

    if ($existingDebt + $creditedAmount > $farmer->credit_limit) {
        return response()->json([
            'success' => false,
            'message' => 'Credit limit exceeded',
            'credit_limit' => $farmer->credit_limit,
            'current_debt' => $existingDebt,
            'new_debt' => $creditedAmount,
        ], 422);
    }




    }

    $transaction = Transaction::create([
    'farmer_id'          => $validated['farmer_id'],
    'operator_id'        => $request->user()->id,
    'total_price_fcfa'   => $total,
    'payment_method'     => $validated['payment_method'],
    'interest_rate'      => $interestRate,
    'credited_amount_fcfa' => $creditedAmount,
]);

foreach ($validated['items'] as $item) {
    TransactionItem::create([
        'transaction_id' => $transaction->id,
        'product_id'     => $item['product_id'],
        'quantity'       => $item['quantity'],
        'unit_price_fcfa' => $products[$item['product_id']]->price_fcfa,
    ]);
}


if ($validated['payment_method'] === 'credit') {
    Debt::create([
        'transaction_id'      => $transaction->id,
        'farmer_id'           => $validated['farmer_id'],
        'original_amount_fcfa' => $creditedAmount,
        'remaining_amount_fcfa' => $creditedAmount,
        'status'              => 'pending',
    ]);}

        return response()->json(['success' => true, 'data' => $transaction], 201);
    }

    public function show($id)
    {
        $transaction = Transaction::with(['items.product', 'farmer', 'debt'])
            ->findOrFail($id);

        return response()->json(['success' => true, 'data' => $transaction]);
    }}
