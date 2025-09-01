<?php

namespace Database\Factories;

use App\Models\Page;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PageInvite>
 */
class PageInviteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'page_id' => Page::factory(),
            'user_id' => User::factory(),
            'handle' => Str::random(8),
            'clicks' => fake()->numberBetween(0, 100),
            'leads_count' => fake()->numberBetween(0, 50),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the invite is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
} 