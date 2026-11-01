<?php

namespace App\Models;

use App\Data\DopplerRingSettings;
use Database\Factories\DopplerRingStyleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A named Doppler rings look for page heroes; pages pick one, and several can share it.
 *
 * @property DopplerRingSettings $settings
 */
#[Fillable(['name', 'settings'])]
class DopplerRingStyle extends Model
{
    /** @use HasFactory<DopplerRingStyleFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'settings' => DopplerRingSettings::class,
        ];
    }

    /** @return HasMany<Page, $this> */
    public function pages(): HasMany
    {
        return $this->hasMany(Page::class);
    }
}
