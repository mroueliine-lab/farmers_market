<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
   protected $fillable = ['transaction_id', 'product_id', 'quantity', 'unit_price_fcfa'];

    protected $casts = [
        'unit_price_fcfa' => 'decimal:2',
    ];

public function product()
{
   return $this->belongsTo(Product::class);
}
   }
