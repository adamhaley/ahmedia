<?php

use App\Data\DopplerRingSettings;
use App\Models\DopplerRingStyle;
use App\Models\Page;
use Illuminate\Database\Migrations\Migration;

/**
 * Gives each remaining top-level page its own still rings, the same recipe as the service
 * pages: the Services style with both jitters maxed, varied only by seed. Pages that already
 * have a style (Home, Services, anything set in the admin) are left alone, and the Lab is
 * skipped because its hero is the interactive lab itself.
 */
return new class extends Migration
{
    public function up(): void
    {
        $base = DopplerRingStyle::query()->where('name', 'Services')->first()?->settings;
        if (! $base) {
            return;
        }

        $seeds = [
            'about' => 911,
            'case-studies' => 84,
            'blog' => 305,
            'faq' => 428,
            'contact' => 593,
            'approach' => 742,
        ];

        foreach ($seeds as $path => $seed) {
            $page = Page::query()->where('path', $path)->first();
            if (! $page || $page->doppler_ring_style_id || $page->hide_doppler_rings) {
                continue;
            }

            $style = DopplerRingStyle::query()->firstOrCreate(['name' => 'Page: '.$page->title], [
                'settings' => DopplerRingSettings::fromArray([...$base->toArray(), 'spacing' => 1, 'size' => 1, 'seed' => $seed, 'motion' => 'still']),
            ]);
            $page->update(['doppler_ring_style_id' => $style->id]);
        }
    }
};
