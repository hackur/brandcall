<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brand_phone_numbers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained()->cascadeOnDelete();
            $table->string('phone_number'); // E.164 format
            $table->string('country_code', 5)->default('US');
            
            // Ownership verification (for A-level attestation)
            $table->string('loa_document_path')->nullable();
            $table->date('loa_verified_at')->nullable();
            $table->enum('ownership_status', ['unverified', 'verified', 'pending'])->default('unverified');
            
            // CNAM Registration
            $table->string('cnam_display_name')->nullable();
            $table->boolean('cnam_registered')->default(false);
            $table->timestamp('cnam_registered_at')->nullable();
            
            // Free Caller Registry
            $table->boolean('fcr_registered')->default(false);
            $table->timestamp('fcr_registered_at')->nullable();
            
            // Analytics provider registrations
            $table->json('analytics_registrations')->nullable();
            
            $table->enum('status', ['active', 'suspended', 'quarantined'])->default('active');
            $table->timestamps();
            
            $table->unique(['brand_id', 'phone_number']);
            $table->index('phone_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('brand_phone_numbers');
    }
};
