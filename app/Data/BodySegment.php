<?php

namespace App\Data;

/**
 * One piece of a page body split at its section tokens (Page::bodySegments()): either Markdown,
 * or the name of the section a `[[token]]` line placed there.
 */
final readonly class BodySegment
{
    public function __construct(
        public string $markdown = '',
        public ?string $section = null,
    ) {}
}
