<?php

namespace App\Models;

use App\Models\Concerns\BelongsToClinic;
use Illuminate\Database\Eloquent\Model;

class ClinicSetting extends Model
{
    use BelongsToClinic;

    protected $fillable = [
        'clinic_id',
        'key',
        'value',
    ];
}
