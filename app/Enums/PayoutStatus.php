<?php

namespace App\Enums;

enum PayoutStatus: string
{
    case Prepared = 'prepared';
    case Approved = 'approved';
    case Processing = 'processing';
    case Paid = 'paid';
    case Failed = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::Prepared => 'Prepared',
            self::Approved => 'Approved',
            self::Processing => 'Processing',
            self::Paid => 'Paid',
            self::Failed => 'Failed',
        };
    }
}
