<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Page>
 */
class PageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(3);
        $slug = Str::slug($title);
        
        return [
            'user_id' => User::factory(),
            'title' => $title,
            'slug' => $slug,
            'headline' => fake()->sentence(6),
            'summary' => fake()->paragraph(2),
            'content' => fake()->paragraphs(3, true),
            'image_url' => fake()->imageUrl(800, 600, 'business'),
            'cta_text' => fake()->words(3, true),
            'cta_subtext' => fake()->sentence(),
            'default_join_url' => fake()->url(),
            'is_public' => true,
            'views' => fake()->numberBetween(0, 1000),
            'leads_count' => fake()->numberBetween(0, 100),
        ];
    }

    /**
     * Indicate that the page is private.
     */
    public function private(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_public' => false,
        ]);
    }
} 