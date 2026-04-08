<?php

namespace App\Enums;

enum DebtStatus: string
{
    case Pending = 'pending';
    case Partial = 'partial';
    case Paid    = 'paid';
}
