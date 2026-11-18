<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sub_leases', function (Blueprint $table) {
            $table->id();
            $table->string('sub_lease_no', 20)->unique();
            $table->foreignId('lease_id')->constrained('leases')->restrictOnDelete();
            $table->foreignId('park_id')->constrained('parks')->restrictOnDelete();
            $table->foreignId('spv_id')->constrained('spvs')->restrictOnDelete();
            $table->foreignId('party_id')->constrained('parties')->restrictOnDelete();
            $table->decimal('capacity_mw', 10, 3);
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('rent_amount', 15, 2)->default(0);
            $table->enum('rent_frequency', ['Annual', 'Half-yearly', 'Quarterly', 'Monthly'])->default('Annual');
            $table->text('payment_terms')->nullable();
            $table->string('agreement_no', 40)->nullable();
            $table->enum('lender_noc_obtained', ['Yes', 'No'])->default('No');
            $table->enum('status', ['Draft', 'Active', 'Expired', 'Cancelled'])->default('Draft');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['lease_id', 'status', 'start_date', 'end_date'], 'sub_leases_capacity_idx');
            $table->index('party_id');
        });

        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plot_id')->nullable()->constrained('plots')->nullOnDelete();
            $table->string('documentable_type', 80);
            $table->unsignedBigInteger('documentable_id');
            $table->string('doc_type', 60);
            $table->string('title', 200);
            $table->string('file_path', 255);
            $table->string('original_name', 255);
            $table->string('mime_type', 120)->nullable();
            $table->unsignedInteger('size_kb')->default(0);
            $table->unsignedSmallInteger('version_no')->default(1);
            $table->enum('is_original_scan', ['Yes', 'No'])->default('Yes');
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['documentable_type', 'documentable_id']);
            $table->index(['plot_id', 'doc_type', 'title']);
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('username', 60)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('module', 40);
            $table->string('table_name', 60)->nullable();
            $table->string('record_id', 40)->nullable();
            $table->string('record_label', 120)->nullable();
            $table->enum('action', ['CREATE', 'UPDATE', 'DELETE', 'EXPORT', 'LOGIN', 'LOGIN_FAILED', 'LOGOUT']);
            $table->string('field_name', 80)->nullable();
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['module', 'record_id']);
            $table->index(['user_id', 'created_at']);
            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('documents');
        Schema::dropIfExists('sub_leases');
    }
};
