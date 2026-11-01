<?php

namespace App\Models;

use App\Enums\HeroRingsMotion;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['contact_email', 'phone', 'accent_color', 'footer_tagline', 'hero_rings_motion', 'hero_rings_scroll_start', 'hero_rings_scroll_end', 'hero_rings_pin_length'])]
class SiteSetting extends Model
{
    /** The teal/cyan glow used sitewide when no custom accent color has been set. */
    public const DefaultAccentColor = '#00ffff';

    /**
     * Mirrors the column defaults, so a row created by current() has them before it's reloaded.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'footer_tagline' => 'AI systems and automation for teams that want to ship, not just plan.',
        'hero_rings_motion' => 'scroll',
        'hero_rings_scroll_start' => 0,
        'hero_rings_scroll_end' => 1,
        'hero_rings_pin_length' => 220,
    ];

    public static function current(): self
    {
        return self::query()->firstOrCreate();
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'hero_rings_motion' => HeroRingsMotion::class,
            'hero_rings_scroll_start' => 'float',
            'hero_rings_scroll_end' => 'float',
            'hero_rings_pin_length' => 'integer',
        ];
    }
}
