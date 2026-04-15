<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case UNPAID   = 'unpaid';
    case PAID     = 'paid';
    case PARTIAL  = 'partial';
    case REFUNDED = 'refunded';
    case FAILED   = 'failed';
}