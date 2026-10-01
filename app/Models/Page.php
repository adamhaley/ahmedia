<?php

namespace App\Models;

use App\Enums\PageTemplate;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property list<string>|null $bullets
 * @property list<array<string, string>>|null $buttons
 */
#[Fillable([
    'parent_id', 'title', 'slug', 'template', 'sort_order', 'is_published', 'show_in_nav', 'nav_anchor',
    'meta_title', 'meta_description', 'hero_heading', 'hero_subheading', 'hero_image', 'og_image',
    'section_eyebrow', 'section_heading', 'body', 'bullets', 'buttons', 'circle_image', 'circle_image_alt',
    'card_excerpt', 'card_image', 'cta_eyebrow', 'cta_heading', 'cta_button_label',
])]
class Page extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'template' => PageTemplate::class,
            'bullets' => 'array',
            'buttons' => 'array',
            'is_published' => 'boolean',
            'show_in_nav' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Page $page): void {
            $page->path = $page->template === PageTemplate::Home
                ? ''
                : ltrim(($page->parent?->path ?? '').'/'.$page->slug, '/');
        });

        static::saved(function (Page $page): void {
            if ($page->wasChanged('path')) {
                $page->children->each(fn (Page $child): bool => $child->setRelation('parent', $page)->save());
            }
        });
    }

    /** @return BelongsTo<Page, $this> */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'parent_id');
    }

    /** @return HasMany<Page, $this> */
    public function children(): HasMany
    {
        return $this->hasMany(Page::class, 'parent_id')->orderBy('sort_order');
    }

    public function url(): string
    {
        return $this->path === '' ? '/' : '/'.$this->path.'/';
    }

    /** @return list<int> */
    public function descendantIds(): array
    {
        return $this->children->flatMap(fn (Page $child): array => [$child->id, ...$child->descendantIds()])->all();
    }
}
