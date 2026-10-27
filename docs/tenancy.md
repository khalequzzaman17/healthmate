# HealthMate Multi-Tenancy Architecture

## 1. Tenancy Model
HealthMate uses a **shared-database, tenant-isolated architecture**. In this architecture:
- Multiple independent clinics reside in the same database schema.
- All clinic-specific tables include a foreign key `clinic_id` referencing the `clinics` table.
- A user can be a member of multiple clinics with different roles in each.

## 2. Tenant Context Resolution (`TenantContext`)
Tenant state is held in `App\Services\TenantContext`. The resolution workflow is handled by `App\Http\Middleware\IdentifyTenant`:

1. When an authenticated request arrives, the middleware checks the user's active session for `active_clinic_id`.
2. If absent or invalid, the middleware retrieves the user's first active `ClinicMembership`.
3. If the user is a platform `SuperAdmin` without clinic memberships, it defaults to the primary platform clinic.
4. The resolved clinic is verified against `ClinicMembership` to guarantee the authenticated user actually belongs to that clinic.
5. The verified clinic is registered in `TenantContext::setClinic($clinic)`.
6. Middleware priority ensures `IdentifyTenant` executes **before** `SubstituteBindings` so that route-model binding automatically applies tenant scopes.

## 3. Global Query Scoping (`ClinicScope` & `BelongsToClinic`)
The `App\Models\Concerns\BelongsToClinic` trait is applied to all tenant-owned models:
```php
trait BelongsToClinic
{
    public static function bootBelongsToClinic(): void
    {
        static::addGlobalScope(new ClinicScope());

        static::creating(function ($model) {
            if (empty($model->clinic_id) && TenantContext::hasClinic()) {
                $model->clinic_id = TenantContext::getClinicId();
            }
        });
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }
}
```

### Automatic Scope Injection
`App\Models\Scopes\ClinicScope` intercepts all Eloquent queries:
```php
public function apply(Builder $builder, Model $model): void
{
    if (TenantContext::hasClinic()) {
        $builder->where($model->qualifyColumn('clinic_id'), TenantContext::getClinicId());
    }
}
```
If Clinic 2 user queries `/patients/1` (which belongs to Clinic 1), the query executes as:
`SELECT * FROM patients WHERE id = 1 AND clinic_id = 2`
This returns `null`, causing Laravel's route-model binder to abort with a standard HTTP 404 (Not Found).

## 4. Tenant Switching
Users with memberships in multiple clinics can switch between clinics using the switch endpoint:
- Route: `POST /switch-clinic/{clinic}` (`route('clinic.switch')`)
- Controller: `App\Http\Controllers\ClinicContextController`
- Validation: Verifies that the user has an active `ClinicMembership` with the target clinic before updating `active_clinic_id` in the session.

## 5. Defense in Depth
1. **Database Constraints**: Foreign keys on `clinic_id` with `cascadeOnDelete`.
2. **Global Scopes**: Automatic query scoping via `ClinicScope`.
3. **Controller Level Checks**: Sensitive endpoints verify `$model->clinic_id === TenantContext::getClinicId()`.
4. **Audit Logging**: All queries and access logs track `clinic_id` for compliance and forensic analysis.
