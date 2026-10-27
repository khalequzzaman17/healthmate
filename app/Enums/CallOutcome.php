<?php

namespace App\Enums;

enum CallOutcome: string
{
    case Completed = 'completed';
    case NoAnswer = 'no_answer';
    case Busy = 'busy';
    case CallbackRequested = 'callback_requested';
    case WrongNumber = 'wrong_number';
    case Declined = 'declined';
    case Escalated = 'escalated';

    public function label(): string
    {
        return match ($this) {
            self::Completed => 'Completed',
            self::NoAnswer => 'No Answer',
            self::Busy => 'Busy / Line Busy',
            self::CallbackRequested => 'Callback Requested',
            self::WrongNumber => 'Wrong Number',
            self::Declined => 'Patient Declined',
            self::Escalated => 'Escalated to Doctor',
        };
    }
}
