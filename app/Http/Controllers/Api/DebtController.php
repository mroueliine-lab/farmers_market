<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Debt;

class DebtController extends Controller
{
     public function index()
    {
        $debts = Debt::with(['farmer', 'transaction'])->paginate(50);
        return response()->json(['success' => true, 'data' => $debts]);
    }
}
