<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parties', function (Blueprint $table) {
            $table->id();
            $table->string('party_code', 20)->unique();
            $table->string('party_name', 150);
            $table->enum('party_type', ['Landowner', 'Company', 'Sub-lessee', 'Other']);
            $table->string('contact_person', 120)->nullable();
            $table->string('mobile', 15)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('address', 255)->nullable();
            $table->foreignId('state_id')->nullable()->constrained('states')->nullOnDelete();
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->foreignId('taluka_id')->nullable()->constrained('talukas')->nullOnDelete();
            $table->string('pincode', 6)->nullable();
            $table->string('pan', 10)->nullable()->index();
            $table->string('gst_no', 15)->nullable();
            $table->char('aadhaar_last4', 4)->nullable();
            $table->string('voter_id', 30)->nullable();
            $table->text('bank_account_no')->nullable(); // encrypted cast
            $table->string('bank_ifsc', 11)->nullable();
            $table->string('bank_name_branch', 150)->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['party_type', 'status']);
            $table->index('party_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parties');
    }
};
