<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['contact_email', 'phone', 'accent_color'])]
class SiteSetting extends Model
{
    /** The teal/cyan glow used sitewide when no custom accent color has been set. */
    public const DefaultAccentColor = '#00ffff';

    public static function current(): self
    {
        return self::query()->firstOrCreate();
    }
}
