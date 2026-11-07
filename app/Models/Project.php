<?php

namespace App\Models;

use App\Enums\ProjectType;
use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A showcase card (an n8n workflow, a website, ...) shown on the page it's attached to,
 * with a cover and gallery, and links to download, buy or view it.
 *
 * @property list<array{label: string, url: string}>|null $links
 * @property list<string>|null $tags
 */
#[Fillable([
    'page_id', 'case_study_id', 'type', 'title', 'slug', 'description', 'cover_image', 'download_file',
    'links', 'tags', 'is_published', 'sort_order',
])]
class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => ProjectType::class,
            'links' => 'array',
            'tags' => 'array',
            'is_published' => 'boolean',
        ];
    }

    /** @param Builder<Project> $query */
    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true)->orderBy('sort_order')->orderBy('id');
    }

    /** @return BelongsTo<Page, $this> */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    /** @return BelongsTo<CaseStudy, $this> */
    public function caseStudy(): BelongsTo
    {
        return $this->belongsTo(CaseStudy::class);
    }

    /** @return HasMany<ProjectImage, $this> */
    public function images(): HasMany
    {
        return $this->hasMany(ProjectImage::class)->orderBy('sort_order');
    }
}
