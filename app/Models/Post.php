<?php

namespace App\Models;

use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/** An Agentic Age blog post */
#[Fillable(['title', 'slug', 'excerpt', 'body', 'cover_image', 'meta_description', 'published_at'])]
class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    /**
     * Posts whose publish date has arrived, newest first.
     *
     * @param  Builder<Post>  $query
     */
    public function scopePublished(Builder $query): void
    {
        $query->where('published_at', '<=', now())->latest('published_at');
    }

    public function isPublished(): bool
    {
        return $this->published_at?->isPast() ?? false;
    }

    public function url(): string
    {
        return '/blog/'.$this->slug;
    }
}
