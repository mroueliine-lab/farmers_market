<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['total_price_fcfa', 'payment_method', 'interest_rate', 'credited_amount_fcfa', 'farmer_id', 'operator_id'];

    protected $casts = [
        'total_price_fcfa'     => 'decimal:2',
        'interest_rate'        => 'decimal:2',
        'credited_amount_fcfa' => 'decimal:2',
        'payment_method'       => PaymentMethod::class,
    ];

public function items()
{
    return $this->hasMany(TransactionItem::class);
}

public function farmer()
{
    return $this->belongsTo(Farmer::class);
}

public function debt()
{
    return $this->hasOne(Debt::class);
}


}

