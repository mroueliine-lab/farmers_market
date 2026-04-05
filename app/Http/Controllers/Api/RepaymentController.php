<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Debt;
use App\Models\Repayment;


class RepaymentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'farmer_id'   => 'required|exists:farmers,id',
            'kg_received' => 'required|numeric|min:0.01',
        ]);

        $commodityRate = Setting::where('key', 'commodity_rate')->value('value') ?? 0;
        $fcfaValue = $validated['kg_received'] * $commodityRate;

        $debts = Debt::where('farmer_id', $validated['farmer_id'])
            ->whereIn('status', ['pending', 'partial'])
            ->orderBy('created_at', 'asc')
            ->get();

        $remaining = $fcfaValue;

        foreach ($debts as $debt) {
            /** @var Debt $debt */
            if ($remaining <= 0) break;

            if ($remaining >= $debt->remaining_amount_fcfa) {
                $remaining -= $debt->remaining_amount_fcfa;
                $debt->remaining_amount_fcfa = 0;
                $debt->status = 'paid';
            } else {
                $debt->remaining_amount_fcfa -= $remaining;
                $debt->status = 'partial';
                $remaining = 0;
            }

            $debt->save();
        }

        $repayment = Repayment::create([
            'farmer_id'      => $validated['farmer_id'],
            'operator_id'    => $request->user()->id,
            'kg_received'    => $validated['kg_received'],
            'commodity_rate' => $commodityRate,
            'fcfa_value'     => $fcfaValue,
        ]);

        return response()->json(['success' => true, 'data' => $repayment], 201);
    }
}
