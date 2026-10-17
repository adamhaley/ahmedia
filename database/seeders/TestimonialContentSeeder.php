<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

/**
 * Seeds the homepage testimonials. Like the other content seeders, it only
 * runs against an empty table so CMS edits are never overwritten.
 */
class TestimonialContentSeeder extends Seeder
{
    public function run(): void
    {
        if (Testimonial::query()->exists()) {
            $this->command?->warn('Testimonials already exist; skipping so CMS edits are never overwritten.');

            return;
        }

        foreach (require __DIR__.'/data/testimonials.php' as $sortOrder => $testimonial) {
            Testimonial::query()->create([...$testimonial, 'sort_order' => $sortOrder]);
        }
    }
}
