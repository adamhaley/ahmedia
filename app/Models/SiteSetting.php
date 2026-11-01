<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['contact_email', 'phone', 'accent_color', 'footer_tagline'])]
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
    ];

    public static function current(): self
    {
        return self::query()->firstOrCreate();
    }
}
