<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('follow_up_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_nurse_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('due_date');
            $table->string('frequency')->default('once'); // once, daily, weekly, biweekly, monthly
            $table->string('priority')->default('normal'); // low, normal, high, urgent
            $table->text('instructions')->nullable();
            $table->string('status')->default('scheduled'); // scheduled, completed, canceled, overdue
            $table->timestamps();

            $table->index(['clinic_id', 'assigned_nurse_id', 'due_date', 'status'], 'fup_plans_nurse_due_status_idx');
        });

        Schema::create('patient_calls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('nurse_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('follow_up_plan_id')->nullable()->constrained()->nullOnDelete();
            $table->dateTime('call_date');
            $table->unsignedInteger('duration_seconds')->default(0);
            $table->boolean('reached')->default(true);
            $table->string('outcome'); // completed, no_answer, busy, callback_requested, wrong_number, declined, escalated
            $table->text('notes')->nullable();
            $table->date('next_follow_up_date')->nullable();
            $table->timestamps();

            $table->index(['clinic_id', 'nurse_id', 'call_date']);
        });

        Schema::create('health_observations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('recorded_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('patient_call_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedSmallInteger('systolic_bp')->nullable();
            $table->unsignedSmallInteger('diastolic_bp')->nullable();
            $table->decimal('blood_glucose', 5, 2)->nullable();
            $table->string('glucose_type')->nullable(); // fasting, random, postprandial
            $table->decimal('temperature_f', 4, 1)->nullable();
            $table->decimal('weight_kg', 5, 2)->nullable();
            $table->unsignedSmallInteger('pulse_rate')->nullable();
            $table->text('symptoms')->nullable();
            $table->string('medication_adherence')->default('good'); // good, partial, poor
            $table->text('patient_concerns')->nullable();
            $table->boolean('escalation_needed')->default(false);
            $table->timestamps();

            $table->index(['clinic_id', 'patient_id', 'created_at']);
        });

        Schema::create('health_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('nurse_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('patient_call_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('summary');
            $table->string('status')->default('submitted'); // submitted, under_review, reviewed, escalated
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by_doctor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('review_notes')->nullable();
            $table->timestamps();

            $table->index(['clinic_id', 'status']);
            $table->index(['clinic_id', 'patient_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('health_reports');
        Schema::dropIfExists('health_observations');
        Schema::dropIfExists('patient_calls');
        Schema::dropIfExists('follow_up_plans');
    }
};
