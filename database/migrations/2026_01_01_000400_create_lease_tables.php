<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** LEASE_MASTER, RENT_SCHEDULE, PAYMENT_REGISTER */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('land_id')->constrained('lands')->restrictOnDelete();
            $table->string('lessor_name', 200);
            $table->string('lessor_pan', 10)->nullable();
            $table->decimal('leased_area_acres', 14, 2);
            $table->decimal('leased_capacity_mw', 10, 3)->default(0);
            $table->enum('lease_status', ['Draft', 'Active', 'Expired', 'Terminated'])->default('Active');
            $table->string('lessor_address', 255)->nullable();
            $table->foreignId('lessor_state_id')->constrained('states')->restrictOnDelete();
            $table->foreignId('lessor_district_id')->constrained('districts')->restrictOnDelete();
            $table->foreignId('lessor_city_id')->constrained('cities')->restrictOnDelete();
            $table->char('lessor_pincode', 6)->nullable();
            $table->date('atl_date')->nullable();
            $table->date('lease_date')->nullable(); // registration date
            $table->date('lease_start_date');
            $table->unsignedSmallInteger('lease_duration_mths');
            $table->date('lease_end_date');
            $table->string('uni_number', 40)->nullable();
            $table->string('sro_doc_no', 40)->nullable();
            $table->string('agreement_no', 40)->nullable();
            $table->string('mutation_entry_no', 40)->nullable();
            $table->decimal('rent_rate_per_acre', 15, 2);
            $table->enum('rent_frequency', ['Annual', 'Half-yearly', 'Quarterly', 'Monthly'])->default('Annual');
            $table->decimal('rent_amount', 15, 2); // year-1 rent per instalment
            $table->decimal('security_deposit', 15, 2)->default(0);
            $table->decimal('escalation_pct', 6, 2)->default(0);
            $table->unsignedTinyInteger('escalation_every_yrs')->default(0);
            $table->enum('tds_applicable', ['Yes', 'No'])->default('Yes');
            $table->decimal('tds_rate', 5, 2)->default(10);
            $table->enum('gst_applicable', ['Yes', 'No'])->default('No');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['land_id', 'lease_status']);
        });

        Schema::create('rent_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lease_id')->constrained('leases')->cascadeOnDelete();
            $table->foreignId('land_id')->constrained('lands')->restrictOnDelete();
            $table->string('due_month', 10);
            $table->date('due_date');
            $table->decimal('rent_amount', 15, 2);
            $table->decimal('tds_amount', 15, 2)->default(0);
            $table->decimal('net_payable', 15, 2);
            $table->enum('payment_status', ['Pending', 'Partial Payment', 'Paid', 'On hold'])->default('Pending');
            $table->enum('alert_sent_flag', ['Yes', 'No'])->default('No');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->index(['lease_id', 'due_date']);
            $table->index(['due_date', 'payment_status']);
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('rent_schedules')->restrictOnDelete();
            $table->foreignId('land_id')->constrained('lands')->restrictOnDelete();
            $table->foreignId('farmer_id')->constrained('land_owners')->restrictOnDelete();
            $table->date('payment_date');
            $table->decimal('gross_amount', 15, 2);
            $table->decimal('tds_deducted', 15, 2)->default(0);
            $table->string('tds_challan_no', 40)->nullable();
            $table->decimal('net_paid', 15, 2);
            $table->string('utr_reference', 60);
            $table->string('sap_voucher_no', 40)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['schedule_id', 'farmer_id']);
            $table->index('payment_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('rent_schedules');
        Schema::dropIfExists('leases');
    }
};
