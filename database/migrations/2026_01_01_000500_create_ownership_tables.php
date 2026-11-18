<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plot_ownership_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plot_id')->constrained('plots')->cascadeOnDelete();
            $table->unsignedSmallInteger('version_no');
            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();
            $table->enum('status', ['Draft', 'Finalised', 'Superseded'])->default('Draft');
            $table->decimal('total_percent', 7, 4)->default(0);
            $table->string('change_reason', 255)->nullable();
            $table->foreignId('finalised_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('finalised_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->unique(['plot_id', 'version_no']);
            $table->index(['plot_id', 'status', 'effective_from']);
        });

        Schema::create('plot_ownership_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ownership_version_id')->constrained('plot_ownership_versions')->cascadeOnDelete();
            $table->foreignId('party_id')->constrained('parties')->restrictOnDelete();
            $table->decimal('share_percent', 7, 4);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->unique(['ownership_version_id', 'party_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plot_ownership_shares');
        Schema::dropIfExists('plot_ownership_versions');
    }
};
