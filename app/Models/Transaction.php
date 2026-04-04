<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['total_price_fcfa', 'payment_method', 'interest_rate', 'credited_amount_fcfa', 'farmer_id',
     'operator_id'];

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

