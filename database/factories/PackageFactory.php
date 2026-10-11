<?php

namespace Database\Factories;

use App\Enums\PageTemplate;
use App\Models\Package;
use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Package>
 */
class PackageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'page_id' => fn (): int => Page::query()->create([
                'title' => fake()->words(2, true),
                'slug' => fake()->unique()->slug(2),
                'template' => PageTemplate::Service,
            ])->id,
            'title' => fake()->catchPhrase(),
            'slug' => fake()->unique()->slug(3),
            'summary' => fake()->sentence(),
            'price' => fake()->numberBetween(5, 20) * 100,
            'delivery_days' => fake()->numberBetween(2, 7),
            'includes' => [fake()->sentence(), fake()->sentence()],
            'add_ons' => [],
            'is_published' => true,
            'sort_order' => 0,
        ];
    }

    public function unpublished(): static
    {
        return $this->state(['is_published' => false]);
    }
}
