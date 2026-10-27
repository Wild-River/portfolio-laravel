<?php

namespace Database\Factories;

use App\Enums\PublishStatus;
use App\Models\Category;
use App\Models\User;
use App\Models\Work;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Work>
 */
class WorkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraphs(3, true),
            'slug' => fake()->unique()->regexify('[a-z]{8}-[0-9]{4}'),
            'status' => PublishStatus::Draft,
            'published_at' => fake()->dateTimeBetween('-1 year')->format('Y-m-d'),
        ];
    }

    /**
     * Indicate that the work is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PublishStatus::Published,
        ]);
    }
}
