<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use PrismCms\Models\Page as PrismPage;

/**
 * ahmedia's pages: Prism CMS core's page tree plus the rings, hero points, side card, approach steps
 * and CTA testimonial.
 *
 * @property list<string>|null $bullets
 * @property list<string>|null $approach_steps
 * @property list<string>|null $hero_points
 * @property array{title?: ?string, link_label?: ?string, link_url?: ?string, note?: ?string}|null $side_card
 * @property list<array<string, string>>|null $buttons
 */
#[Fillable([
    'parent_id', 'title', 'slug', 'template', 'doppler_ring_style_id', 'hide_doppler_rings', 'sort_order', 'is_published', 'show_in_nav', 'nav_anchor',
    'meta_title', 'meta_description', 'hero_heading', 'hero_subheading', 'hero_points', 'hero_image', 'side_card', 'og_image',
    'section_eyebrow', 'section_heading', 'body', 'bullets', 'approach_steps', 'buttons', 'circle_image', 'circle_image_alt',
    'card_excerpt', 'card_image', 'cta_eyebrow', 'cta_heading', 'cta_copy', 'cta_button_label', 'cta_testimonial_id',
])]
class Page extends PrismPage
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            ...parent::casts(),
            'approach_steps' => 'array',
            'hero_points' => 'array',
            'side_card' => 'array',
            'hide_doppler_rings' => 'boolean',
        ];
    }

    /** @return BelongsTo<Testimonial, $this> */
    public function ctaTestimonial(): BelongsTo
    {
        return $this->belongsTo(Testimonial::class, 'cta_testimonial_id');
    }

    /** @return BelongsTo<DopplerRingStyle, $this> */
    public function dopplerRingStyle(): BelongsTo
    {
        return $this->belongsTo(DopplerRingStyle::class);
    }

    /** The rings style the hero shows: the page's own, else its nearest ancestor's, unless rings are turned off. */
    public function heroRingStyle(): ?DopplerRingStyle
    {
        if ($this->hide_doppler_rings) {
            return null;
        }

        return $this->dopplerRingStyle ?? $this->parent?->heroRingStyle();
    }

    /** @return HasMany<Package, $this> */
    public function packages(): HasMany
    {
        return $this->hasMany(Package::class)->orderBy('sort_order');
    }

    /** @return HasMany<Credential, $this> */
    public function credentials(): HasMany
    {
        return $this->hasMany(Credential::class);
    }

    /** @return HasMany<Project, $this> */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class)->orderBy('sort_order');
    }

    /** The homepage approach diagram's steps while its field is empty */
    public const DefaultApproachSteps = ['Discover what works', "Remove what's in the way", 'Install the multiplier'];

    /** The supporting line under a page's call-to-action heading when the CMS leaves it empty. */
    public const DefaultCtaCopy = "Tell us about the bottleneck. You work directly with Adam, and we'll tell you honestly whether AI is the right tool for it.";

    /** ahmedia's page URLs have no trailing slash. */
    public function url(): string
    {
        return $this->path === '' ? '/' : '/'.$this->path;
    }
}
