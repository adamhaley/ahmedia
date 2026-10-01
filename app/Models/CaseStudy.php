<?php

namespace App\Models;

use Database\Factories\CaseStudyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property list<string>|null $tags
 */
#[Fillable([
    'title', 'slug', 'summary', 'narrative', 'hero_image', 'external_link', 'tags', 'client_name',
    'started_at', 'completed_at', 'testimonial_quote', 'testimonial_author', 'testimonial_author_role',
    'is_published', 'sort_order', 'source_project_id',
])]
class CaseStudy extends Model
{
    /** @use HasFactory<CaseStudyFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'started_at' => 'date',
            'completed_at' => 'date',
            'is_published' => 'boolean',
        ];
    }

    /** @return HasMany<CaseStudyImage, $this> */
    public function images(): HasMany
    {
        return $this->hasMany(CaseStudyImage::class)->orderBy('sort_order');
    }
}
