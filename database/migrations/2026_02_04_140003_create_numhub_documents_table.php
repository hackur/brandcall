<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create numhub_documents table for document tracking.
 *
 * Tracks documents uploaded to NumHub for BCID verification.
 * Links to local documents table when applicable.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('numhub_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('numhub_entity_id')
                ->constrained('numhub_entities')
                ->cascadeOnDelete();
            $table->foreignId('local_document_id')
                ->nullable()
                ->constrained('documents')
                ->nullOnDelete();

            // NumHub identifier (returned after upload)
            $table->string('numhub_document_id', 100)->nullable();

            // Document details
            $table->string('document_type', 50); // loa|business_license|tax_id|government_id|other
            $table->string('filename');

            // Status tracking
            $table->string('status', 20)->default('pending'); // pending|uploaded|verified|rejected

            // API response cache
            $table->json('api_response')->nullable();

            // When uploaded to NumHub
            $table->timestamp('uploaded_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('numhub_entity_id');
            $table->index('local_document_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('numhub_documents');
    }
};
