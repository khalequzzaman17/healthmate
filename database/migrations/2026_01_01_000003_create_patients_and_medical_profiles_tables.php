<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // optional patient portal user account
            $table->string('patient_number'); // Clinic-specific (e.g. HM-2026-0001)
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->default('other'); // male, female, other
            $table->string('blood_group')->nullable(); // A+, B+, etc.
            $table->string('phone');
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relation')->nullable();
            $table->text('medical_history_summary')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['clinic_id', 'patient_number']);
            $table->index(['clinic_id', 'phone']);
        });

        Schema::create('patient_allergies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->string('allergen');
            $table->string('severity')->default('mild'); // mild, moderate, severe
            $table->string('reaction')->nullable();
            $table->timestamps();
        });

        Schema::create('patient_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->string('condition_name');
            $table->date('diagnosed_date')->nullable();
            $table->string('status')->default('active'); // active, resolved
            $table->timestamps();
        });

        Schema::create('patient_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('nurse_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assigned_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('active'); // active, inactive
            $table->timestamps();

            $table->index(['clinic_id', 'nurse_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_assignments');
        Schema::dropIfExists('patient_conditions');
        Schema::dropIfExists('patient_allergies');
        Schema::dropIfExists('patients');
    }
};
