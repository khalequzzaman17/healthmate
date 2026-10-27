<?php

namespace App\Services;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\AppointmentStatusHistory;
use App\Models\Clinic;
use App\Models\Patient;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AppointmentService
{
    /**
     * Check if a doctor is available at the requested time slot.
     */
    public function isDoctorAvailable(
        int $clinicId,
        int $doctorId,
        Carbon $scheduledAt,
        int $durationMinutes,
        ?int $ignoreAppointmentId = null
    ): bool {
        $slotStart = $scheduledAt->copy();
        $slotEnd = $scheduledAt->copy()->addMinutes($durationMinutes);

        // Check active overlapping appointments for the same doctor
        $overlapping = Appointment::where('clinic_id', $clinicId)
            ->where('doctor_id', $doctorId)
            ->whereIn('status', [
                AppointmentStatus::Scheduled->value,
                AppointmentStatus::CheckedIn->value,
                AppointmentStatus::InConsultation->value,
            ])
            ->when($ignoreAppointmentId, fn ($query) => $query->where('id', '!=', $ignoreAppointmentId))
            ->where(function ($query) use ($slotStart, $slotEnd) {
                $query->where(function ($q) use ($slotStart, $slotEnd) {
                    $q->where('scheduled_at', '<', $slotEnd);
                    if (DB::getDriverName() === 'sqlite') {
                        $q->whereRaw("datetime(scheduled_at, '+' || duration_minutes || ' minute') > ?", [$slotStart->toDateTimeString()]);
                    } else {
                        $q->whereRaw('DATE_ADD(scheduled_at, INTERVAL duration_minutes MINUTE) > ?', [$slotStart->toDateTimeString()]);
                    }
                });
            })
            ->exists();

        return ! $overlapping;
    }

    /**
     * Book an appointment with concurrency check and transactional integrity.
     */
    public function bookAppointment(
        Clinic $clinic,
        Patient $patient,
        User $doctor,
        Carbon $scheduledAt,
        ?Service $service = null,
        int $durationMinutes = 20,
        string $bookingSource = 'staff',
        ?string $notes = null
    ): Appointment {
        return DB::transaction(function () use (
            $clinic,
            $patient,
            $doctor,
            $scheduledAt,
            $service,
            $durationMinutes,
            $bookingSource,
            $notes
        ) {
            // Check availability with a lock on Doctor appointments for concurrency safety
            $slotStart = $scheduledAt->copy();
            $slotEnd = $scheduledAt->copy()->addMinutes($durationMinutes);

            $hasOverlap = Appointment::where('clinic_id', $clinic->id)
                ->where('doctor_id', $doctor->id)
                ->whereIn('status', [
                    AppointmentStatus::Scheduled->value,
                    AppointmentStatus::CheckedIn->value,
                    AppointmentStatus::InConsultation->value,
                ])
                ->where(function ($query) use ($slotStart, $slotEnd) {
                    $query->where('scheduled_at', '<', $slotEnd);
                    if (DB::getDriverName() === 'sqlite') {
                        $query->whereRaw("datetime(scheduled_at, '+' || duration_minutes || ' minute') > ?", [$slotStart->toDateTimeString()]);
                    } else {
                        $query->whereRaw('DATE_ADD(scheduled_at, INTERVAL duration_minutes MINUTE) > ?', [$slotStart->toDateTimeString()]);
                    }
                })
                ->lockForUpdate()
                ->exists();

            if ($hasOverlap) {
                throw ValidationException::withMessages([
                    'scheduled_at' => 'The selected doctor already has an active appointment at this time.',
                ]);
            }

            $appointment = Appointment::create([
                'clinic_id' => $clinic->id,
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'service_id' => $service?->id,
                'scheduled_at' => $scheduledAt,
                'duration_minutes' => $durationMinutes,
                'status' => AppointmentStatus::Scheduled,
                'booking_source' => $bookingSource,
                'notes' => $notes,
            ]);

            AppointmentStatusHistory::create([
                'appointment_id' => $appointment->id,
                'from_status' => 'none',
                'to_status' => AppointmentStatus::Scheduled->value,
                'changed_by_user_id' => auth()->id(),
                'reason' => 'Initial booking',
            ]);

            AuditService::log('appointment.booked', $appointment, null, [
                'doctor_id' => $doctor->id,
                'patient_id' => $patient->id,
                'scheduled_at' => $scheduledAt->toDateTimeString(),
            ], $clinic->id);

            return $appointment;
        });
    }

    /**
     * Transition an appointment to a new status.
     */
    public function transitionStatus(
        Appointment $appointment,
        AppointmentStatus $targetStatus,
        ?User $user = null,
        ?string $reason = null
    ): Appointment {
        $currentStatus = $appointment->status;

        if (! $currentStatus->canTransitionTo($targetStatus)) {
            throw ValidationException::withMessages([
                'status' => "Cannot transition appointment from {$currentStatus->label()} to {$targetStatus->label()}.",
            ]);
        }

        return DB::transaction(function () use ($appointment, $currentStatus, $targetStatus, $user, $reason) {
            $appointment->update([
                'status' => $targetStatus,
                'cancellation_reason' => $targetStatus === AppointmentStatus::Canceled ? $reason : $appointment->cancellation_reason,
            ]);

            AppointmentStatusHistory::create([
                'appointment_id' => $appointment->id,
                'from_status' => $currentStatus->value,
                'to_status' => $targetStatus->value,
                'changed_by_user_id' => $user?->id ?? auth()->id(),
                'reason' => $reason,
            ]);

            AuditService::log('appointment.status_changed', $appointment, ['status' => $currentStatus->value], [
                'status' => $targetStatus->value,
                'reason' => $reason,
            ], $appointment->clinic_id);

            return $appointment;
        });
    }
}
