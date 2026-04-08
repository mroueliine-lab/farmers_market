<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Debt;
use App\Models\TransactionItem;
use App\Models\Farmer;

class TransactionController extends Controller
{

public function index()
    {
        $transactions = Transaction::with(['farmer', 'items.product'])->latest()->get();

        return response()->json(['success' => true, 'data' => $transactions]);
    }
    
public function store(StoreTransactionRequest $request)
    {
        $validated = $request->validated();

        $productIds = collect($validated['items'])->pluck('product_id');
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        $total = 0;
        foreach ($validated['items'] as $item) {
            $total += $products[$item['product_id']]->price_fcfa * $item['quantity'];
        }

        $transaction = DB::transaction(function () use ($validated, $products, $total, $request) {
            $interestRate = 0;
            $creditedAmount = 0;

            if ($validated['payment_method'] === 'credit') {
                // Lock the farmer row to prevent concurrent credit limit bypass
                $farmer = Farmer::lockForUpdate()->findOrFail($validated['farmer_id']);

                $interestRate = Setting::where('key', 'interest_rate')->value('value') ?? 0;
                $creditedAmount = $total * (1 + $interestRate / 100);

                $existingDebt = $farmer->debts()
                    ->whereIn('status', ['pending', 'partial'])
                    ->sum('remaining_amount_fcfa');

                if ($existingDebt + $creditedAmount > $farmer->credit_limit) {
                    abort(422, json_encode([
                        'success'      => false,
                        'message'      => 'Credit limit exceeded',
                        'credit_limit' => $farmer->credit_limit,
                        'current_debt' => $existingDebt,
                        'new_debt'     => $creditedAmount,
                    ]));
                }
            }

            $transaction = Transaction::create([
                'farmer_id'            => $validated['farmer_id'],
                'operator_id'          => $request->user()->id,
                'total_price_fcfa'     => $total,
                'payment_method'       => $validated['payment_method'],
                'interest_rate'        => $interestRate,
                'credited_amount_fcfa' => $creditedAmount,
            ]);

            foreach ($validated['items'] as $item) {
                TransactionItem::create([
                    'transaction_id'  => $transaction->id,
                    'product_id'      => $item['product_id'],
                    'quantity'        => $item['quantity'],
                    'unit_price_fcfa' => $products[$item['product_id']]->price_fcfa,
                ]);
            }

            if ($validated['payment_method'] === 'credit') {
                Debt::create([
                    'transaction_id'       => $transaction->id,
                    'farmer_id'            => $validated['farmer_id'],
                    'original_amount_fcfa'  => $creditedAmount,
                    'remaining_amount_fcfa' => $creditedAmount,
                    'status'               => 'pending',
                ]);
            }

            return $transaction;
        });

        return response()->json(['success' => true, 'data' => $transaction], 201);
    }

    public function show($id)
    {
        $transaction = Transaction::with(['items.product', 'farmer', 'debt'])
            ->findOrFail($id);

        return response()->json(['success' => true, 'data' => $transaction]);
    }}
