<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** SUBLEASE_MASTER, COMPLIANCE_TRACKER, DOCUMENT_REPOSITORY, AUDIT_TRAIL */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subleases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lease_id')->constrained('leases')->restrictOnDelete();
            $table->foreignId('land_id')->constrained('lands')->restrictOnDelete();
            $table->string('sublessee_name', 150);
            $table->string('sublessee_pan', 10)->nullable();
            $table->decimal('sublease_area_acres', 14, 2);
            $table->decimal('capacity_mw', 10, 3)->default(0);
            $table->decimal('sublease_rent', 15, 2)->nullable();
            $table->date('sublease_start');
            $table->date('sublease_end');
            $table->string('agreement_no', 40)->nullable();
            $table->enum('lender_noc_obtained', ['Yes', 'No'])->default('No');
            $table->enum('status', ['Draft', 'Active', 'Terminated'])->default('Active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['lease_id', 'status', 'sublease_start', 'sublease_end'], 'subleases_capacity_idx');
        });

        Schema::create('compliances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('land_id')->unique()->constrained('lands')->restrictOnDelete();
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
            $table->string('escalation_level', 30)->default('L0 · None');
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('land_id')->constrained('lands')->restrictOnDelete();
            $table->enum('linked_entity_type', ['Land', 'Survey', 'Farmer', 'Lease', 'Sublease', 'Compliance', 'Payment'])->default('Land');
            $table->unsignedBigInteger('linked_entity_id');
            $table->string('doc_type', 60);
            $table->string('doc_title', 200);
            $table->string('file_name', 255);
            $table->string('file_path', 255);
            $table->unsignedInteger('file_size_kb')->default(0);
            $table->string('mime_type', 120)->nullable();
            $table->unsignedSmallInteger('version_no')->default(1);
            $table->enum('is_original_scan', ['Yes', 'No'])->default('Yes');
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['land_id', 'linked_entity_type', 'linked_entity_id']);
            $table->index(['land_id', 'doc_type', 'doc_title']);
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('username', 60)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('module', 40);
            $table->string('table_name', 60)->nullable();
            $table->string('record_id', 60)->nullable();
            $table->string('record_label', 120)->nullable();
            $table->enum('action', ['CREATE', 'UPDATE', 'DELETE', 'EXPORT', 'LOGIN', 'LOGIN_FAILED', 'LOGOUT']);
            $table->string('field_name', 80)->nullable();
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['module', 'record_label']);
            $table->index(['user_id', 'created_at']);
            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('documents');
        Schema::dropIfExists('compliances');
        Schema::dropIfExists('subleases');
    }
};
