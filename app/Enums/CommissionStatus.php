<?php

namespace App\Enums;

enum CommissionStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Payable = 'payable';
    case Paid = 'paid';
    case Reversed = 'reversed';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending Review',
            self::Approved => 'Approved',
            self::Payable => 'Payable',
            self::Paid => 'Paid',
            self::Reversed => 'Reversed',
            self::Rejected => 'Rejected',
        };
    }
}
