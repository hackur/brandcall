<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Registration Drafts - Stores in-progress registration wizard data.
 *
 * Allows users to save their progress and resume registration later.
 * Identified by session_id (for anonymous users) or email (for returning users).
 * Data is transferred to User model upon successful registration.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registration_drafts', function (Blueprint $table) {
            $table->id();

            // Identification - either session or email
            $table->string('session_id')->nullable()->index();
            $table->string('email')->nullable()->index();

            // Current wizard step (1-4)
            $table->unsignedTinyInteger('current_step')->default(1);

            // Step 1: Account Info
            $table->string('name')->nullable();
            // email is above (also used for identification)
            // password not stored in draft for security

            // Step 2: Business Info
            $table->string('company_name')->nullable();
            $table->string('company_website')->nullable();
            $table->string('company_phone')->nullable();
            $table->string('company_address')->nullable();
            $table->string('company_city')->nullable();
            $table->string('company_state')->nullable();
            $table->string('company_zip')->nullable();
            $table->string('company_country')->default('US');

            // Step 3: KYC / Qualification
            $table->string('industry')->nullable();
            $table->string('monthly_call_volume')->nullable();
            $table->string('use_case')->nullable();
            $table->string('current_provider')->nullable();
            $table->string('uses_stir_shaken')->nullable();

            // Step 4: Phone Numbers
            $table->string('primary_phone')->nullable();
            $table->string('phone_ownership')->nullable();

            // Metadata
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('last_saved_at')->nullable();
            $table->timestamps();

            // Composite index for faster lookups
            $table->index(['session_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registration_drafts');
    }
};
