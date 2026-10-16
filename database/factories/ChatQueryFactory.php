<?php

namespace Database\Factories;

use App\Enums\FaqMatchOutcome;
use App\Models\ChatQuery;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ChatQuery>
 */
class ChatQueryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'question' => rtrim(fake()->sentence(), '.').'?',
            'outcome' => FaqMatchOutcome::NoMatch,
        ];
    }
}
