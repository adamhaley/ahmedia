<?php

namespace App\Models;

use Database\Factories\PackageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A fixed-price offer shown on a service page.
 *
 * @property list<string>|null $includes
 * @property list<array{title: string, description?: string|null, price: int|string, days?: int|string|null}>|null $add_ons
 */
#[Fillable([
    'page_id', 'title', 'slug', 'summary', 'price', 'delivery_days', 'includes', 'add_ons',
    'sample_document', 'upwork_url', 'is_published', 'sort_order',
])]
class Package extends Model
{
    /** @use HasFactory<PackageFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'delivery_days' => 'integer',
            'includes' => 'array',
            'add_ons' => 'array',
            'is_published' => 'boolean',
        ];
    }

    /** @return BelongsTo<Page, $this> */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function url(): string
    {
        return $this->page->url().'#'.$this->slug;
    }
}
