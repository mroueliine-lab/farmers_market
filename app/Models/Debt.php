<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Farmer;
use App\Models\Transaction;
use App\Models\Repayment;

class Debt extends Model
{
    protected $fillable = [
        'farmer_id',
                'transaction_id',
                'repayment_id',
        'original_amount_fcfa',
        'remaining_amount_fcfa',
        'status',

    ];

    public function farmer()
{
    return $this->belongsTo(Farmer::class);
}

public function transaction()
{
    return $this->belongsTo(Transaction::class);
}

public function repayment()
{
    return $this->belongsTo(Repayment::class);
}

}
