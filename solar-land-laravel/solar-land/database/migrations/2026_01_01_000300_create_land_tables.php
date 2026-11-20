<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * LAND_MASTER (one land parcel, with its one-to-one grid connectivity),
 * LAND_SURVEY (survey / plot numbers of the parcel), LAND_OWNER (farmers with ownership %).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lands', function (Blueprint $table) {
            $table->id();
            $table->string('land_ref_no', 60)->unique(); // SPV-STATE-PARK-PARCEL-YEAR, immutable
            $table->foreignId('park_id')->nullable()->constrained('parks')->restrictOnDelete();
            $table->foreignId('spv_id')->constrained('spvs')->restrictOnDelete();
            $table->foreignId('state_id')->constrained('states')->restrictOnDelete();
            $table->foreignId('district_id')->constrained('districts')->restrictOnDelete();
            $table->foreignId('city_id')->constrained('cities')->restrictOnDelete();
            $table->string('parcel_code', 10);
            $table->unsignedSmallInteger('ref_year');
            $table->decimal('area_sqm_712', 14, 2);
            $table->decimal('area_acres_712', 14, 2);
            $table->decimal('area_under_lease_acres', 14, 2);
            $table->decimal('dlr_sqm', 14, 2)->nullable();
            $table->decimal('rate_per_acre', 15, 2);
            $table->unsignedTinyInteger('increment_after_yrs')->default(3);
            $table->decimal('increment_pct', 6, 2)->default(5);
            $table->enum('title_report', ['Yes', 'No'])->default('No');
            $table->enum('title_dd_status', ['Pending', 'In progress', 'Clear', 'Issue found'])->default('Pending');
            $table->enum('lessor_capacity_verified', ['Yes', 'No'])->default('No');
            $table->enum('encumbrance_status', ['Under verification', 'Nil', 'Encumbered'])->default('Under verification');
            $table->text('lien_details')->nullable();
            $table->enum('na_application', ['Yes', 'No'])->default('No');
            $table->enum('na_status', ['Not applied', 'Applied', 'Approved', 'Rejected'])->default('Not applied');
            $table->enum('land_status', ['Identified', 'ATL signed', 'Leased', 'Subleased', 'Mortgaged', 'Dropped'])->default('Identified');
            // Grid connectivity (one record per parcel, edited in its own tab)
            $table->decimal('connectivity_mw', 10, 3)->nullable();
            $table->string('substation', 120)->nullable();
            $table->string('aggregator', 120)->nullable();
            $table->enum('tfr_confirmation', ['Yes', 'No'])->default('No');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['spv_id', 'land_status']);
            $table->index('park_id');
        });

        Schema::create('land_surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('land_id')->constrained('lands')->restrictOnDelete();
            $table->foreignId('state_id')->constrained('states')->restrictOnDelete();
            $table->foreignId('district_id')->constrained('districts')->restrictOnDelete();
            $table->foreignId('city_id')->constrained('cities')->restrictOnDelete();
            $table->string('village', 100);
            $table->string('survey_plot_no', 30);
            $table->decimal('area_sqm', 14, 2);
            $table->decimal('area_acres', 14, 2);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['land_id', 'village', 'survey_plot_no']);
        });

        Schema::create('land_owners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('land_id')->constrained('lands')->restrictOnDelete();
            $table->string('owner_name', 150);
            $table->decimal('ownership_pct', 7, 4);
            $table->string('owner_pan', 10)->nullable();
            $table->string('aadhaar', 20)->nullable();   // stored masked: XXXX-XXXX-1234
            $table->string('voter_id', 30)->nullable();
            $table->text('bank_ac')->nullable();         // encrypted
            $table->string('bank_branch', 150)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['land_id', 'owner_pan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('land_owners');
        Schema::dropIfExists('land_surveys');
        Schema::dropIfExists('lands');
    }
};
