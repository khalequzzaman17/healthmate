<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
            $table->foreignId('appointment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('health_report_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->text('chief_complaints')->nullable();
            $table->text('examination_findings')->nullable();
            $table->text('clinical_notes')->nullable();
            $table->text('advice')->nullable();
            $table->string('status')->default('completed'); // in_progress, completed
            $table->timestamps();

            $table->index(['clinic_id', 'doctor_id', 'completed_at']);
            $table->index(['clinic_id', 'patient_id']);
        });

        Schema::create('diagnoses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
            $table->foreignId('consultation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->string('diagnosis_code')->nullable();
            $table->string('diagnosis_name');
            $table->string('type')->default('primary'); // primary, secondary, provisional
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
            $table->foreignId('consultation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->string('prescription_number'); // e.g. RX-2026-0001
            $table->text('instructions')->nullable();
            $table->timestamp('issued_at');
            $table->timestamps();

            $table->unique(['clinic_id', 'prescription_number']);
            $table->index(['clinic_id', 'patient_id']);
        });

        Schema::create('prescription_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescription_id')->constrained()->cascadeOnDelete();
            $table->string('medicine_name');
            $table->string('strength')->nullable(); // e.g. 500mg
            $table->string('dosage_form')->default('tablet'); // tablet, capsule, syrup, injection, drops
            $table->string('dosage_instructions'); // e.g. 1+0+1, twice daily
            $table->string('route')->default('oral'); // oral, IV, IM, topical
            $table->unsignedSmallInteger('duration_days')->default(7);
            $table->text('special_instructions')->nullable(); // before meals, after meals
            $table->timestamps();
        });

        Schema::create('medical_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('uploaded_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('consultation_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('health_report_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('file_path');
            $table->string('file_type');
            $table->unsignedBigInteger('file_size');
            $table->string('category')->default('lab_report'); // lab_report, radiology, discharge_summary, other
            $table->timestamps();

            $table->index(['clinic_id', 'patient_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_attachments');
        Schema::dropIfExists('prescription_items');
        Schema::dropIfExists('prescriptions');
        Schema::dropIfExists('diagnoses');
        Schema::dropIfExists('consultations');
    }
};
