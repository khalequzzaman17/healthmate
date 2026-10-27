<?php

namespace App\Enums;

enum ClinicRole: string
{
    case Admin = 'admin';
    case Doctor = 'doctor';
    case Nurse = 'nurse';
    case Receptionist = 'receptionist';
    case Accountant = 'accountant';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Clinic Admin',
            self::Doctor => 'Doctor',
            self::Nurse => 'Nurse',
            self::Receptionist => 'Receptionist',
            self::Accountant => 'Accountant',
        };
    }
}
