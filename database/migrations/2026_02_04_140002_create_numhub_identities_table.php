<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create numhub_identities table for display identity mapping.
 *
 * Tracks caller ID display configurations (NumhubIdentityId).
 * Each identity belongs to an entity and can have multiple phone numbers.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('numhub_identities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('numhub_entity_id')
                ->constrained('numhub_entities')
                ->cascadeOnDelete();

            // NumHub identifier
            $table->string('numhub_identity_id', 100)->unique();

            // Display information
            $table->string('display_name', 32); // CNAM display (max 32 chars per spec)
            $table->string('call_reason')->nullable();
            $table->string('logo_url', 500)->nullable();

            // Status
            $table->string('status', 20)->default('pending'); // active|inactive|pending

            // Rich Call Data configuration
            $table->json('rich_call_data')->nullable();

            // Associated phone numbers (TN array)
            $table->json('phone_numbers')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['numhub_entity_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('numhub_identities');
    }
};
