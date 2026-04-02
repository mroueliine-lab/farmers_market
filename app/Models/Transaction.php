<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['total_price_fcfa', 'payment_method', 'interest_rate', 'credited_amount_fcfa', 'farmer_id', 'operator_id'];
}
