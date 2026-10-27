<?php

namespace App\Services;

use App\Models\Clinic;

class TenantContext
{
    protected static ?Clinic $currentClinic = null;

    public static function setClinic(?Clinic $clinic): void
    {
        static::$currentClinic = $clinic;
    }

    public static function getClinic(): ?Clinic
    {
        return static::$currentClinic;
    }

    public static function getClinicId(): ?int
    {
        return static::$currentClinic?->id;
    }

    public static function hasClinic(): bool
    {
        return static::$currentClinic !== null;
    }

    public static function clear(): void
    {
        static::$currentClinic = null;
    }

    /**
     * Run a callback within the context of a specific clinic.
     *
     * @template T
     *
     * @param  callable(): T  $callback
     * @return T
     */
    public static function runInClinicContext(Clinic $clinic, callable $callback): mixed
    {
        $previous = static::$currentClinic;
        static::setClinic($clinic);

        try {
            return $callback();
        } finally {
            static::setClinic($previous);
        }
    }
}
