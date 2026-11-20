<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** PARK_MASTER, SPV_MASTER, user SPV access */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parks', function (Blueprint $table) {
            $table->id();
            $table->string('park_code', 6)->unique();
            $table->string('park_name', 150);
            $table->foreignId('state_id')->constrained('states')->restrictOnDelete();
            $table->foreignId('district_id')->constrained('districts')->restrictOnDelete();
            $table->foreignId('city_id')->constrained('cities')->restrictOnDelete();
            $table->decimal('total_area_acres', 14, 2)->nullable();
            $table->decimal('installed_capacity_mw', 10, 3)->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('spvs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('state_id')->constrained('states')->restrictOnDelete();
            $table->foreignId('park_id')->constrained('parks')->restrictOnDelete();
            $table->string('spv_code', 12)->unique();
            $table->string('spv_name', 150);
            $table->enum('technology', ['Solar PV', 'Wind', 'Hybrid'])->default('Solar PV');
            $table->decimal('capacity_mwp', 10, 3)->nullable();
            $table->enum('status', ['Development', 'Under construction', 'Commissioned'])->default('Development');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('park_id');
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
