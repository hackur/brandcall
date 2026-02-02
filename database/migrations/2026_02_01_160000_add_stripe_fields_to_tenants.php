<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('website')->nullable()->after('phone');
            // Note: stripe_subscription_id/item_id removed - using Cashier's subscriptions table instead
        });

        Schema::table('usage_records', function (Blueprint $table) {
            $table->integer('last_synced_count')->default(0)->after('stripe_usage_record_id');
            $table->timestamp('last_synced_at')->nullable()->after('last_synced_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['phone', 'website']);
        });

        Schema::table('usage_records', function (Blueprint $table) {
            $table->dropColumn(['last_synced_count', 'last_synced_at']);
        });
    }
};
