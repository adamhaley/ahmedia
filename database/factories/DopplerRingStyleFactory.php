<?php

namespace Database\Factories;

use App\Data\DopplerRingSettings;
use App\Enums\HeroRingsMotion;
use App\Models\DopplerRingStyle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DopplerRingStyle>
 */
class DopplerRingStyleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'settings' => DopplerRingSettings::defaults(),
        ];
    }

    public function autoMotion(): static
    {
        return $this->state(fn (): array => [
            'settings' => DopplerRingSettings::fromArray(['motion' => HeroRingsMotion::Auto->value]),
        ]);
    }
}
