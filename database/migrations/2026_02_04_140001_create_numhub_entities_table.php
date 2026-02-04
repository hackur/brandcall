<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create numhub_entities table for BCID application mapping.
 *
 * Maps BrandCall tenants/brands to NumHub BCID applications.
 * The numhub_entity_id column stores the NumhubEntityId from the API.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('numhub_entities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('tenant_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('brand_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // NumHub identifiers
            $table->string('numhub_entity_id', 100)->unique(); // NumhubEntityId from API
            $table->string('numhub_eid', 50)->nullable(); // Enterprise ID (EID)

            // Entity details
            $table->string('entity_type', 20)->default('enterprise'); // enterprise|bpo|direct
            $table->string('company_name');

            // Status tracking
            $table->string('status', 30)->default('draft'); // draft|submitted|pending_vetting|approved|rejected
            $table->string('vetting_status', 30)->default('pending'); // pending|in_review|approved|rejected
            $table->char('attestation_level', 1)->nullable(); // A|B|C (STIR/SHAKEN)

            // API response cache
            $table->json('api_response')->nullable();

            // Timestamps
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['tenant_id', 'status']);
            $table->index('brand_id');
            $table->index('numhub_eid');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('numhub_entities');
    }
};
