<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add NumHub integration fields to tenants table.
 *
 * These fields enable tenant-level NumHub configuration:
 * - numhub_client_id: The NumHub client identifier for API auth
 * - numhub_osp_id: OSP/Reseller ID if operating as a reseller
 * - numhub_enabled: Feature flag to enable/disable NumHub for tenant
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('numhub_client_id', 100)->nullable()->after('settings');
            $table->string('numhub_osp_id', 100)->nullable()->after('numhub_client_id');
            $table->boolean('numhub_enabled')->default(false)->after('numhub_osp_id');

            // Index for quick lookups
            $table->index('numhub_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropIndex(['numhub_enabled']);
            $table->dropColumn([
                'numhub_client_id',
                'numhub_osp_id',
                'numhub_enabled',
            ]);
        });
    }
};
