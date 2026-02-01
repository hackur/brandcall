<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Landing page with customizable content, colors, and layouts.
 *
 * @property int $id
 * @property string $slug
 * @property string $name
 * @property bool $is_active
 * @property string $headline
 * @property string|null $subheadline
 * @property string|null $description
 * @property string $cta_text
 * @property string|null $cta_url
 * @property string|null $hero_image
 * @property string|null $hero_video_url
 * @property array|null $features
 * @property array|null $testimonials
 * @property bool $show_pricing
 * @property string|null $pricing_headline
 * @property bool $show_contact_form
 * @property string $form_headline
 * @property array|null $form_fields
 * @property string $layout_preset
 * @property string $color_scheme
 * @property array|null $custom_colors
 * @property string|null $logo_url
 * @property string|null $favicon_url
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $og_image
 * @property string|null $utm_source
 * @property string|null $utm_medium
 * @property string|null $utm_campaign
 * @property int $view_count
 * @property int $conversion_count
 */
class LandingPage extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'is_active',
        'headline',
        'subheadline',
        'description',
        'cta_text',
        'cta_url',
        'hero_image',
        'hero_video_url',
        'features',
        'testimonials',
        'show_pricing',
        'pricing_headline',
        'show_contact_form',
        'form_headline',
        'form_fields',
        'layout_preset',
        'color_scheme',
        'custom_colors',
        'logo_url',
        'favicon_url',
        'meta_title',
        'meta_description',
        'og_image',
        'utm_source',
        'utm_medium',
        'utm_campaign',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'features' => 'array',
        'testimonials' => 'array',
        'show_pricing' => 'boolean',
        'show_contact_form' => 'boolean',
        'form_fields' => 'array',
        'custom_colors' => 'array',
        'view_count' => 'integer',
        'conversion_count' => 'integer',
    ];

    /**
     * Available layout presets.
     */
    public const LAYOUT_PRESETS = [
        'hero-left' => 'Hero Left (Text left, image right)',
        'hero-right' => 'Hero Right (Image left, text right)',
        'hero-center' => 'Hero Center (Centered content)',
        'hero-fullwidth' => 'Hero Fullwidth (Full-screen hero)',
        'minimal' => 'Minimal (Clean, simple)',
        'corporate' => 'Corporate (Professional, structured)',
        'startup' => 'Startup (Modern, bold)',
        'saas' => 'SaaS (Feature-focused)',
    ];

    /**
     * Available color schemes.
     */
    public const COLOR_SCHEMES = [
        'blue' => [
            'primary' => '#2563eb',
            'secondary' => '#1e40af',
            'accent' => '#3b82f6',
            'background' => '#f8fafc',
            'text' => '#1e293b',
        ],
        'green' => [
            'primary' => '#16a34a',
            'secondary' => '#15803d',
            'accent' => '#22c55e',
            'background' => '#f0fdf4',
            'text' => '#14532d',
        ],
        'purple' => [
            'primary' => '#7c3aed',
            'secondary' => '#6d28d9',
            'accent' => '#8b5cf6',
            'background' => '#faf5ff',
            'text' => '#3b0764',
        ],
        'orange' => [
            'primary' => '#ea580c',
            'secondary' => '#c2410c',
            'accent' => '#f97316',
            'background' => '#fff7ed',
            'text' => '#431407',
        ],
        'teal' => [
            'primary' => '#0d9488',
            'secondary' => '#0f766e',
            'accent' => '#14b8a6',
            'background' => '#f0fdfa',
            'text' => '#134e4a',
        ],
        'red' => [
            'primary' => '#dc2626',
            'secondary' => '#b91c1c',
            'accent' => '#ef4444',
            'background' => '#fef2f2',
            'text' => '#450a0a',
        ],
        'dark' => [
            'primary' => '#3b82f6',
            'secondary' => '#1e40af',
            'accent' => '#60a5fa',
            'background' => '#0f172a',
            'text' => '#f1f5f9',
        ],
    ];

    /**
     * Default form fields configuration.
     */
    public const DEFAULT_FORM_FIELDS = [
        ['name' => 'name', 'label' => 'Full Name', 'type' => 'text', 'required' => true],
        ['name' => 'email', 'label' => 'Email Address', 'type' => 'email', 'required' => true],
        ['name' => 'phone', 'label' => 'Phone Number', 'type' => 'tel', 'required' => false],
        ['name' => 'company', 'label' => 'Company', 'type' => 'text', 'required' => false],
        ['name' => 'message', 'label' => 'Message', 'type' => 'textarea', 'required' => false],
    ];

    /**
     * Get leads submitted through this landing page.
     */
    public function leads(): HasMany
    {
        return $this->hasMany(LandingPageLead::class);
    }

    /**
     * Get the resolved colors (custom or from scheme).
     *
     * @return array<string, string>
     */
    public function getColorsAttribute(): array
    {
        if ($this->custom_colors && count($this->custom_colors) > 0) {
            return array_merge(self::COLOR_SCHEMES['blue'], $this->custom_colors);
        }

        return self::COLOR_SCHEMES[$this->color_scheme] ?? self::COLOR_SCHEMES['blue'];
    }

    /**
     * Get form fields with defaults.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getFormFieldsWithDefaults(): array
    {
        return $this->form_fields ?? self::DEFAULT_FORM_FIELDS;
    }

    /**
     * Increment view count.
     */
    public function recordView(): void
    {
        $this->increment('view_count');
    }

    /**
     * Increment conversion count.
     */
    public function recordConversion(): void
    {
        $this->increment('conversion_count');
    }

    /**
     * Get conversion rate as percentage.
     */
    public function getConversionRateAttribute(): float
    {
        if ($this->view_count === 0) {
            return 0.0;
        }

        return round(($this->conversion_count / $this->view_count) * 100, 2);
    }

    /**
     * Get the full URL for this landing page.
     */
    public function getUrlAttribute(): string
    {
        return url("/lp/{$this->slug}");
    }

    /**
     * Find active landing page by slug.
     */
    public static function findBySlug(string $slug): ?self
    {
        return self::where('slug', $slug)
            ->where('is_active', true)
            ->first();
    }
}
