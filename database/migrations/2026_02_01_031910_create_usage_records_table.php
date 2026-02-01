<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usage_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->year('year');
            $table->tinyInteger('month'); // 1-12
            $table->integer('call_count')->default(0);
            $table->integer('successful_calls')->default(0);
            $table->integer('failed_calls')->default(0);
            $table->integer('spam_flagged_calls')->default(0);
            $table->decimal('total_cost', 10, 2)->default(0);
            $table->decimal('tier_price', 10, 4)->nullable();
            $table->string('stripe_usage_record_id')->nullable();
            $table->boolean('synced_to_stripe')->default(false);
            $table->timestamps();
            
            $table->unique(['tenant_id', 'year', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usage_records');
    }
};
