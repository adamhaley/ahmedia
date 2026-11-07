<?php

namespace Database\Factories;

use App\Models\Credential;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Credential>
 */
class CredentialFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'issuer' => fake()->company(),
            'issued_on' => fake()->date(),
            'url' => fake()->url(),
            'is_active' => true,
        ];
    }

    public function featured(string $value = '100%'): static
    {
        return $this->state(['is_featured' => true, 'value' => $value]);
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
