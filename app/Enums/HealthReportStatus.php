<?php

namespace App\Enums;

enum HealthReportStatus: string
{
    case Submitted = 'submitted';
    case UnderReview = 'under_review';
    case Reviewed = 'reviewed';
    case Escalated = 'escalated';

    public function label(): string
    {
        return match ($this) {
            self::Submitted => 'Submitted (Awaiting Doctor)',
            self::UnderReview => 'Under Review',
            self::Reviewed => 'Reviewed',
            self::Escalated => 'Escalated Urgently',
        };
    }
}
