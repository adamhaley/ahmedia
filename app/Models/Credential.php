<?php

namespace App\Models;

use Database\Factories\CredentialFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/** A certification or track-record stat shown on the About page; featured ones are the big stat tiles */
#[Fillable(['name', 'issuer', 'value', 'issued_on', 'url', 'badge_image', 'is_featured', 'sort_order', 'is_active'])]
class Credential extends Model
{
    /** @use HasFactory<CredentialFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'issued_on' => 'date',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    /** @param Builder<Credential> $query */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }
}
