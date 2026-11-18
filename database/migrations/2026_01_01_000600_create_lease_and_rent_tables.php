<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leases', function (Blueprint $table) {
            $table->id();
            $table->string('lease_no', 20)->unique();
            $table->date('lease_date')->nullable();
            $table->foreignId('park_id')->constrained('parks')->restrictOnDelete();
            $table->foreignId('spv_id')->constrained('spvs')->restrictOnDelete();
            $table->foreignId('plot_id')->constrained('plots')->restrictOnDelete();
            $table->date('atl_date')->nullable();
            $table->date('registration_date')->nullable();
            $table->date('lease_start_date');
            $table->unsignedSmallInteger('lease_duration_months');
            $table->date('lease_end_date');
            $table->decimal('total_lease_area', 14, 4);
            $table->enum('land_unit', ['Acre', 'Hectare', 'Sq.m'])->default('Acre');
            $table->decimal('lease_area_acres', 14, 4);
            $table->decimal('leased_capacity_mw', 10, 3)->default(0);
            $table->enum('rent_type', ['Per acre', 'Fixed annual'])->default('Per acre');
            $table->decimal('rent_rate', 15, 2);
            $table->decimal('annual_rent', 15, 2);
            $table->enum('payment_frequency', ['Annual', 'Half-yearly', 'Quarterly', 'Monthly'])->default('Annual');
            $table->decimal('escalation_pct', 6, 2)->default(0);
            $table->unsignedTinyInteger('escalation_every_yrs')->default(0);
            $table->decimal('security_deposit', 15, 2)->default(0);
            $table->enum('tds_applicable', ['Yes', 'No'])->default('Yes');
            $table->decimal('tds_rate', 5, 2)->default(10);
            $table->enum('gst_applicable', ['Yes', 'No'])->default('No');
            $table->string('uni_number', 40)->nullable();
            $table->string('sro_doc_no', 40)->nullable();
            $table->string('agreement_no', 40)->nullable();
            $table->string('mutation_entry_no', 40)->nullable();
            $table->enum('status', ['Draft', 'Active', 'Expired', 'Terminated'])->default('Draft');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['plot_id', 'status']);
            $table->index(['park_id', 'status', 'lease_start_date', 'lease_end_date'], 'leases_capacity_idx');
            $table->index('spv_id');
        });

        Schema::create('rent_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lease_id')->constrained('leases')->cascadeOnDelete();
            $table->foreignId('plot_id')->constrained('plots')->restrictOnDelete();
            $table->unsignedSmallInteger('installment_no');
            $table->date('period_from');
            $table->date('period_to');
            $table->date('due_date');
            $table->decimal('rent_amount', 15, 2);
            $table->decimal('tds_amount', 15, 2)->default(0);
            $table->decimal('net_payable', 15, 2);
            $table->foreignId('ownership_version_id')->nullable()->constrained('plot_ownership_versions')->nullOnDelete();
            $table->enum('status', ['Pending', 'Partially paid', 'Paid', 'On hold'])->default('Pending');
            $table->enum('alert_sent', ['Yes', 'No'])->default('No');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->unique(['lease_id', 'installment_no']);
            $table->index(['due_date', 'status']);
        });

        Schema::create('rent_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rent_schedule_id')->constrained('rent_schedules')->cascadeOnDelete();
            $table->foreignId('party_id')->constrained('parties')->restrictOnDelete();
            $table->decimal('share_percent', 7, 4);
            $table->decimal('gross_amount', 15, 2);
            $table->decimal('tds_amount', 15, 2)->default(0);
            $table->decimal('net_amount', 15, 2);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->timestamps();
            $table->unique(['rent_schedule_id', 'party_id']);
            $table->index('party_id');
        });

        Schema::create('rent_payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_no', 20)->unique();
            $table->foreignId('rent_allocation_id')->constrained('rent_allocations')->restrictOnDelete();
            $table->date('payment_date')->index();
            $table->decimal('gross_amount', 15, 2);
            $table->decimal('tds_deducted', 15, 2)->default(0);
            $table->string('tds_challan_no', 40)->nullable();
            $table->decimal('net_paid', 15, 2);
            $table->enum('payment_mode', ['NEFT', 'RTGS', 'Cheque', 'UPI', 'Other'])->default('NEFT');
            $table->string('utr_reference', 60)->index();
            $table->string('sap_voucher_no', 40)->nullable();
            $table->string('remarks', 255)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rent_payments');
        Schema::dropIfExists('rent_allocations');
        Schema::dropIfExists('rent_schedules');
        Schema::dropIfExists('leases');
    }
};
