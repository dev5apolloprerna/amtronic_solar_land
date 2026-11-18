<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parks', function (Blueprint $table) {
            $table->id();
            $table->string('park_code', 6)->unique();
            $table->string('park_name', 150);
            $table->foreignId('state_id')->constrained('states')->restrictOnDelete();
            $table->foreignId('city_id')->constrained('cities')->restrictOnDelete();
            $table->foreignId('taluka_id')->constrained('talukas')->restrictOnDelete();
            $table->string('address', 255)->nullable();
            $table->decimal('total_land_area', 14, 4)->default(0);
            $table->enum('land_unit', ['Acre', 'Hectare', 'Sq.m'])->default('Acre');
            $table->decimal('total_land_acres', 14, 4)->default(0);
            $table->decimal('installed_capacity_mw', 10, 3)->default(0);
            $table->text('description')->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active')->index();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('spvs', function (Blueprint $table) {
            $table->id();
            $table->string('spv_code', 12)->unique();
            $table->string('spv_name', 150);
            $table->foreignId('park_id')->constrained('parks')->restrictOnDelete();
            $table->foreignId('party_id')->nullable()->constrained('parties')->nullOnDelete();
            $table->string('cin', 21)->nullable();
            $table->date('incorporation_date')->nullable();
            $table->string('registered_address', 255)->nullable();
            $table->string('contact_person', 120)->nullable();
            $table->string('mobile', 15)->nullable();
            $table->string('email', 150)->nullable();
            $table->enum('technology', ['Solar PV', 'Wind', 'Hybrid'])->default('Solar PV');
            $table->decimal('capacity_mwp', 10, 3)->nullable();
            $table->enum('project_status', ['Development', 'Under construction', 'Commissioned'])->default('Development');
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('user_spv_access', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('spv_id')->constrained('spvs')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'spv_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_spv_access');
        Schema::dropIfExists('spvs');
        Schema::dropIfExists('parks');
    }
};
