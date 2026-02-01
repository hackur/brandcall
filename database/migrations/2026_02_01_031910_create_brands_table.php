<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // "Health Insurance Florida"
            $table->string('slug')->unique(); // "health-insurance-florida"
            $table->string('display_name')->nullable(); // 32-char CNAM display
            $table->string('logo_path')->nullable(); // S3 path
            $table->string('call_reason')->nullable(); // "Appointment Reminder"
            $table->json('rich_call_data')->nullable(); // colors, secondary logo
            
            // NumHub BrandControl IDs
            $table->string('numhub_enterprise_id')->nullable();
            $table->string('numhub_vetting_status')->default('pending'); // pending, approved, rejected
            $table->string('bcid_trust_product_sid')->nullable();
            
            // API Authentication
            $table->string('api_key')->unique();
            $table->string('api_secret')->nullable();
            $table->timestamp('api_key_last_rotated_at')->nullable();
            
            // STIR/SHAKEN Configuration
            $table->enum('default_attestation_level', ['A', 'B', 'C'])->default('A');
            $table->boolean('stir_shaken_enabled')->default(true);
            
            $table->enum('status', ['draft', 'pending_vetting', 'active', 'suspended'])->default('draft');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['tenant_id', 'status']);
            $table->index('numhub_enterprise_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};
