<?php

namespace App\Models;

use App\Enums\DebtStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Farmer;
use App\Models\Transaction;
use App\Models\Repayment;

class Debt extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'farmer_id',
                'transaction_id',
                'repayment_id',
        'original_amount_fcfa',
        'remaining_amount_fcfa',
        'status',
    ];

    protected $casts = [
        'original_amount_fcfa'  => 'decimal:2',
        'remaining_amount_fcfa' => 'decimal:2',
        'status'                => DebtStatus::class,
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
