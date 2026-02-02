<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tenant>
 */
class TenantFactory extends Factory
{
    protected $model = Tenant::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->company();

        return [
            'name' => $name,
            'email' => fake()->unique()->companyEmail(),
            'slug' => Str::slug($name) . '-' . Str::random(5),
            'phone' => fake()->phoneNumber(),
            'website' => fake()->url(),
            'subscription_tier' => 'starter',
            'monthly_call_limit' => 1000,
            'analytics_monitoring_enabled' => false,
        ];
    }

    /**
     * Configure the tenant as a pro subscriber.
     */
    public function pro(): static
    {
        return $this->state(fn (array $attributes) => [
            'subscription_tier' => 'pro',
            'monthly_call_limit' => 10000,
            'analytics_monitoring_enabled' => true,
        ]);
    }

    /**
     * Configure the tenant as an enterprise subscriber.
     */
    public function enterprise(): static
    {
        return $this->state(fn (array $attributes) => [
            'subscription_tier' => 'enterprise',
            'monthly_call_limit' => null, // Unlimited
            'analytics_monitoring_enabled' => true,
        ]);
    }
}
