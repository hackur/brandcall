<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('email_verified_at');
            $table->string('phone')->nullable()->after('status');
            
            // Company info
            $table->string('company_name')->nullable()->after('phone');
            $table->string('company_website')->nullable();
            $table->string('company_phone')->nullable();
            $table->string('company_address')->nullable();
            $table->string('company_city')->nullable();
            $table->string('company_state')->nullable();
            $table->string('company_zip')->nullable();
            
            // Qualification info
            $table->string('industry')->nullable();
            $table->string('monthly_call_volume')->nullable();
            $table->string('use_case')->nullable();
            $table->string('current_provider')->nullable();
            $table->string('uses_stir_shaken')->nullable();
            
            // KYC tracking
            $table->timestamp('kyc_submitted_at')->nullable();
            $table->timestamp('kyc_approved_at')->nullable();
            $table->timestamp('onboarding_completed_at')->nullable();
            
            // Indexes
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'status',
                'phone',
                'company_name',
                'company_website',
                'company_phone',
                'company_address',
                'company_city',
                'company_state',
                'company_zip',
                'industry',
                'monthly_call_volume',
                'use_case',
                'current_provider',
                'uses_stir_shaken',
                'kyc_submitted_at',
                'kyc_approved_at',
                'onboarding_completed_at',
            ]);
        });
    }
};
