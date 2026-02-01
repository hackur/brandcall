<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Company name
            $table->string('email')->unique();
            $table->string('slug')->unique(); // For subdomain/API routing
            $table->string('stripe_customer_id')->nullable();
            $table->string('subscription_tier')->default('starter'); // starter, growth, enterprise
            $table->integer('monthly_call_limit')->nullable(); // null = unlimited
            $table->boolean('analytics_monitoring_enabled')->default(false);
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Add tenant_id to users table
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->string('role')->default('member')->after('password'); // owner, admin, member
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropColumn(['tenant_id', 'role']);
        });
        
        Schema::dropIfExists('tenants');
    }
};
