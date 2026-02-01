<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('call_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('brand_id')->constrained()->cascadeOnDelete();
            $table->foreignId('brand_phone_number_id')->nullable()->constrained()->nullOnDelete();
            
            $table->string('call_id')->unique(); // Our internal ID (call_uuid)
            $table->string('external_call_sid')->nullable(); // NumHub call SID
            $table->string('from_number');
            $table->string('to_number');
            $table->string('direction')->default('outbound');
            
            // STIR/SHAKEN attestation
            $table->enum('attestation_level', ['A', 'B', 'C'])->nullable();
            $table->boolean('stir_shaken_verified')->default(false);
            $table->text('identity_header')->nullable(); // SHAKEN PASSporT
            
            // Call status
            $table->enum('status', ['initiated', 'ringing', 'in-progress', 'completed', 'failed', 'busy', 'no-answer'])->default('initiated');
            $table->string('failure_reason')->nullable();
            
            // Branding delivered
            $table->boolean('branded_call')->default(false);
            $table->json('rcd_payload')->nullable(); // Rich Call Data sent
            
            // Timing
            $table->timestamp('call_initiated_at')->nullable();
            $table->timestamp('call_answered_at')->nullable();
            $table->timestamp('call_ended_at')->nullable();
            $table->integer('ring_duration_seconds')->nullable();
            $table->integer('talk_duration_seconds')->nullable();
            $table->integer('total_duration_seconds')->nullable();
            
            // Spam detection
            $table->json('spam_scores')->nullable();
            $table->boolean('flagged_as_spam')->default(false);
            $table->string('spam_label')->nullable();
            
            // Billing
            $table->decimal('cost', 10, 4)->default(0);
            $table->decimal('tier_price', 10, 4)->nullable(); // Price at time of call
            $table->boolean('billable')->default(true);
            
            // Response data
            $table->json('numhub_response')->nullable();
            $table->json('carrier_metadata')->nullable();
            
            $table->timestamps();
            
            $table->index(['tenant_id', 'created_at']);
            $table->index(['brand_id', 'status']);
            $table->index(['from_number', 'created_at']);
            $table->index('call_initiated_at');
            $table->index(['flagged_as_spam', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('call_logs');
    }
};
