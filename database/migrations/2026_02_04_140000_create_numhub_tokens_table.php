<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create numhub_tokens table for OAuth token caching.
 *
 * Stores NumHub API access tokens with automatic expiry tracking.
 * Tokens can be global (tenant_id = null) or tenant-scoped.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('numhub_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();

            $table->string('token_type', 20)->default('access'); // access|refresh
            $table->text('access_token'); // Encrypted at model level
            $table->text('refresh_token')->nullable(); // Encrypted at model level
            $table->timestamp('expires_at');
            $table->json('scopes')->nullable(); // Granted API scopes

            $table->timestamps();

            // Index for finding valid tokens
            $table->index('tenant_id');
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('numhub_tokens');
    }
};
