<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create numhub_sync_logs table for API audit trail.
 *
 * Records all API interactions for debugging and compliance.
 * Consider partitioning by date or setting up auto-cleanup in production.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('numhub_sync_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Optional relationships
            $table->foreignId('tenant_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->foreignUuid('numhub_entity_id')
                ->nullable()
                ->constrained('numhub_entities')
                ->nullOnDelete();
            $table->foreignUuid('numhub_identity_id')
                ->nullable()
                ->constrained('numhub_identities')
                ->nullOnDelete();

            // Request details
            $table->string('endpoint'); // API endpoint path
            $table->string('method', 10); // GET|POST|PUT|DELETE
            $table->string('direction', 10)->default('outbound'); // outbound|inbound (webhook)

            // Response details
            $table->smallInteger('status_code')->nullable();
            $table->json('request_payload')->nullable(); // Redacted sensitive data
            $table->json('response_payload')->nullable();
            $table->unsignedInteger('response_time_ms')->nullable();

            // Error tracking
            $table->text('error_message')->nullable();
            $table->boolean('success')->default(false);

            // Only created_at - logs are immutable
            $table->timestamp('created_at')->useCurrent();

            // Indexes for common queries
            $table->index(['tenant_id', 'created_at']);
            $table->index(['endpoint', 'success']);
            $table->index('created_at'); // For cleanup/retention queries
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('numhub_sync_logs');
    }
};
