<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
    protected $fillable = [
        'farmer_id',
        'original_amount_fcfa',
        'remaining_amount_fcfa',
        'status',
        'transaction_id',
    ];
}
