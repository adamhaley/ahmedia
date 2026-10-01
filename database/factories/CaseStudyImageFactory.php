<?php

namespace Database\Factories;

use App\Models\CaseStudy;
use App\Models\CaseStudyImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CaseStudyImage>
 */
class CaseStudyImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'case_study_id' => CaseStudy::factory(),
            'image' => 'case-studies/placeholder.jpg',
            'sort_order' => 0,
        ];
    }
}
