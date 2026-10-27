<?php

namespace App\Models\Concerns;

use App\Models\Clinic;
use App\Models\Scopes\ClinicScope;
use App\Services\TenantContext;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToClinic
{
    /**
     * Boot the BelongsToClinic trait.
     */
    public static function bootBelongsToClinic(): void
    {
        static::addGlobalScope(new ClinicScope);

        static::creating(function ($model) {
            if (empty($model->clinic_id) && TenantContext::hasClinic()) {
                $model->clinic_id = TenantContext::getClinicId();
            }
        });
    }

    /**
     * Relationship to the clinic.
     */
    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }
}
