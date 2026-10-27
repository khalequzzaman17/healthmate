<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commission_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
            $table->string('staff_role'); // nurse, doctor
            $table->string('activity_type'); // nurse_follow_up, doctor_consultation
            $table->string('calculation_type')->default('fixed'); // fixed, percentage
            $table->decimal('amount', 10, 2); // e.g. 50.00 BDT or 15.00 (%)
            $table->boolean('is_approval_required')->default(true);
            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['clinic_id', 'staff_role', 'activity_type', 'is_active'], 'comm_rules_role_act_active_idx');
        });

        Schema::create('commission_ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
            $table->foreignId('staff_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('activity_type'); // nurse_follow_up, doctor_consultation
            $table->string('source_type'); // morph type (e.g. HealthReport, Consultation)
            $table->unsignedBigInteger('source_id');
            $table->foreignId('commission_rule_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('calculation_basis', 10, 2)->default(0); // service price or base amount
            $table->decimal('rate_applied', 10, 2); // snapshot of rate applied
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('BDT');
            $table->string('status')->default('pending'); // pending, approved, payable, paid, reversed, rejected
            $table->string('idempotency_key')->unique();
            $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('reversal_reason')->nullable();
            $table->timestamps();

            $table->index(['clinic_id', 'staff_user_id', 'status']);
            $table->index(['source_type', 'source_id']);
        });

        Schema::create('commission_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
            $table->string('payout_number'); // e.g. PO-2026-0001
            $table->foreignId('staff_user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('total_amount', 10, 2);
            $table->string('currency')->default('BDT');
            $table->string('status')->default('prepared'); // prepared, approved, processing, paid, failed
            $table->foreignId('prepared_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->string('payment_method')->nullable(); // bank_transfer, bKash, cash, etc.
            $table->string('payment_reference')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['clinic_id', 'payout_number']);
            $table->index(['clinic_id', 'staff_user_id', 'status']);
        });

        Schema::create('commission_payout_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commission_payout_id')->constrained()->cascadeOnDelete();
            $table->foreignId('commission_ledger_entry_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->timestamps();

            // Crucial: A commission entry can only belong to one payout item
            $table->unique('commission_ledger_entry_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_payout_items');
        Schema::dropIfExists('commission_payouts');
        Schema::dropIfExists('commission_ledger_entries');
        Schema::dropIfExists('commission_rules');
    }
};
