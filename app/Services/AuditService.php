<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Patient;
use App\Models\RecordAccessLog;
use App\Scopes\TenantContext;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditService
{
    /**
     * Log an action in the system audit log.
     */
    public static function log(
        string $action,
        ?EloquentModel $auditable = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?int $clinicId = null
    ): AuditLog {
        $resolvedClinicId = $clinicId ?? TenantContext::getClinicId();

        return AuditLog::create([
            'clinic_id' => $resolvedClinicId,
            'user_id' => Auth::id(),
            'action' => $action,
            'auditable_type' => $auditable ? get_class($auditable) : null,
            'auditable_id' => $auditable?->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Log access or download of a sensitive medical record.
     */
    public static function logAccess(
        int $patientId,
        string $resourceType,
        int $resourceId,
        string $action = 'viewed',
        ?int $clinicId = null
    ): RecordAccessLog {
        $resolvedClinicId = $clinicId
            ?? TenantContext::getClinicId()
            ?? Patient::withoutGlobalScopes()->where('id', $patientId)->value('clinic_id');

        return RecordAccessLog::create([
            'clinic_id' => $resolvedClinicId,
            'user_id' => Auth::id() ?? 1,
            'patient_id' => $patientId,
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
            'action' => $action,
        ]);
    }
}
