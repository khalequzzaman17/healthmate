<?php

namespace App\Enums;

enum AppointmentStatus: string
{
    case Scheduled = 'scheduled';
    case CheckedIn = 'checked_in';
    case InConsultation = 'in_consultation';
    case Completed = 'completed';
    case Canceled = 'canceled';
    case NoShow = 'no_show';

    public function label(): string
    {
        return match ($this) {
            self::Scheduled => 'Scheduled',
            self::CheckedIn => 'Checked In',
            self::InConsultation => 'In Consultation',
            self::Completed => 'Completed',
            self::Canceled => 'Canceled',
            self::NoShow => 'No Show',
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return match ($this) {
            self::Scheduled => in_array($target, [self::CheckedIn, self::Canceled, self::NoShow]),
            self::CheckedIn => in_array($target, [self::InConsultation, self::Canceled, self::NoShow]),
            self::InConsultation => in_array($target, [self::Completed, self::Canceled]),
            self::Completed, self::Canceled, self::NoShow => false,
        };
    }
}
