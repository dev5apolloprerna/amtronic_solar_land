<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Plot = one survey number. Holds the immutable Land Reference Number.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plots', function (Blueprint $table) {
            $table->id();
            $table->string('land_ref_no', 60)->unique();
            $table->foreignId('park_id')->constrained('parks')->restrictOnDelete();
            $table->foreignId('spv_id')->constrained('spvs')->restrictOnDelete();
            $table->foreignId('state_id')->constrained('states')->restrictOnDelete();
            $table->foreignId('city_id')->constrained('cities')->restrictOnDelete();
            $table->foreignId('taluka_id')->constrained('talukas')->restrictOnDelete();
            $table->string('village', 100);
            $table->string('survey_no', 30);
            $table->string('sub_survey_no', 30)->default(''); // '' instead of NULL so the unique index works
            $table->decimal('total_area', 14, 4);
            $table->enum('land_unit', ['Acre', 'Hectare', 'Sq.m'])->default('Acre');
            $table->decimal('area_acres', 14, 4);
            $table->decimal('area_under_lease_acres', 14, 4)->default(0);
            $table->decimal('dlr_area_sqm', 14, 2)->nullable();
            $table->decimal('rate_per_acre', 15, 2)->default(0);
            $table->unsignedTinyInteger('increment_after_yrs')->default(3);
            $table->decimal('increment_pct', 6, 2)->default(5);
            $table->enum('title_report', ['Yes', 'No'])->default('No');
            $table->enum('title_dd_status', ['Pending', 'In progress', 'Clear', 'Issue found'])->default('Pending');
            $table->enum('lessor_capacity_verified', ['Yes', 'No'])->default('No');
            $table->enum('encumbrance_status', ['Under verification', 'Nil', 'Encumbered'])->default('Under verification');
            $table->text('lien_details')->nullable();
            $table->enum('na_application', ['Yes', 'No'])->default('No');
            $table->enum('na_status', ['Not applied', 'Applied', 'Approved', 'Rejected'])->default('Not applied');
            $table->enum('land_status', ['Identified', 'ATL signed', 'Leased', 'Sub-leased', 'Mortgaged', 'Dropped'])->default('Identified');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['taluka_id', 'village', 'survey_no', 'sub_survey_no'], 'plots_survey_unique');
            $table->index(['park_id', 'land_status']);
            $table->index('spv_id');
        });

        Schema::create('plot_grid_connectivity', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plot_id')->unique()->constrained('plots')->cascadeOnDelete();
            $table->decimal('connectivity_mw', 10, 3)->nullable();
            $table->string('substation', 120)->nullable();
            $table->string('aggregator', 120)->nullable();
            $table->enum('tfr_confirmation', ['Yes', 'No'])->default('No');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });

        Schema::create('compliance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plot_id')->unique()->constrained('plots')->restrictOnDelete();
            $table->enum('mortgage_created', ['Yes', 'No'])->default('No');
            $table->date('mortgage_date')->nullable();
            $table->string('lender_name', 150)->nullable();
            $table->string('cersai_reg_no', 40)->nullable();
            $table->enum('original_docs_custody', ['With company', 'With lender', 'With custodian'])->default('With company');
            $table->string('custodian_name', 150)->nullable();
            $table->date('docs_deposit_date')->nullable();
            $table->enum('lender_noc_status', ['Not required', 'Pending', 'Obtained'])->default('Not required');
            $table->unsignedSmallInteger('pdc_open_items')->default(0);
            $table->date('target_closure_date')->nullable();
            $table->enum('escalation_level', ['L0 · None', 'L1 · Land head', 'L2 · CFO', 'L3 · MD'])->default('L0 · None');
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compliance_records');
        Schema::dropIfExists('plot_grid_connectivity');
        Schema::dropIfExists('plots');
    }
};
