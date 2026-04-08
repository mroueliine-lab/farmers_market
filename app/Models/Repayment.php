<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Repayment extends Model
{
    protected $fillable = ['kg_received', 'commodity_rate', 'fcfa_value', 'farmer_id', 'operator_id'];

    protected $casts = [
        'kg_received'    => 'decimal:2',
        'commodity_rate' => 'decimal:2',
        'fcfa_value'     => 'decimal:2',
    ];
}
