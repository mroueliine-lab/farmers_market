<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Debt;
use App\Models\Repayment;

class Farmer extends Model
{
    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'phone_number',
        'credit_limit',
        'identifier'

    ];

    public function debts()
    {
        return $this->hasMany(Debt::class);
    }

    public function repayments()
    {
        return $this->hasMany(Repayment::class);
    }


}
