<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->boolean('is_active')->default(true);

            // Content
            $table->string('headline');
            $table->string('subheadline')->nullable();
            $table->text('description')->nullable();
            $table->string('cta_text')->default('Get Started');
            $table->string('cta_url')->nullable();

            // Hero section
            $table->string('hero_image')->nullable();
            $table->string('hero_video_url')->nullable();

            // Features (JSON array)
            $table->json('features')->nullable();

            // Testimonials (JSON array)
            $table->json('testimonials')->nullable();

            // Pricing display
            $table->boolean('show_pricing')->default(false);
            $table->string('pricing_headline')->nullable();

            // Contact form
            $table->boolean('show_contact_form')->default(true);
            $table->string('form_headline')->default('Get in Touch');
            $table->json('form_fields')->nullable();

            // Design
            $table->string('layout_preset')->default('hero-left');
            $table->string('color_scheme')->default('blue');
            $table->json('custom_colors')->nullable();

            // Branding
            $table->string('logo_url')->nullable();
            $table->string('favicon_url')->nullable();

            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('og_image')->nullable();

            // Tracking
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();

            // Analytics
            $table->unsignedInteger('view_count')->default(0);
            $table->unsignedInteger('conversion_count')->default(0);

            $table->timestamps();
        });

        // Lead submissions from landing pages
        Schema::create('landing_page_leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landing_page_id')->constrained()->cascadeOnDelete();

            $table->string('name')->nullable();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->text('message')->nullable();
            $table->json('custom_fields')->nullable();

            // Tracking
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('referrer')->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();

            // Status
            $table->string('status')->default('new');
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_page_leads');
        Schema::dropIfExists('landing_pages');
    }
};
