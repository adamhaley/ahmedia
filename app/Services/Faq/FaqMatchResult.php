<?php

declare(strict_types=1);

namespace App\Services\Faq;

use App\Enums\FaqMatchOutcome;

readonly class FaqMatchResult
{
    /**
     * @param  list<FaqMatch>  $matches
     */
    public function __construct(
        public FaqMatchOutcome $outcome,
        public array $matches = [],
    ) {}

    public static function noMatch(): self
    {
        return new self(FaqMatchOutcome::NoMatch);
    }

    public function best(): ?FaqMatch
    {
        return $this->matches[0] ?? null;
    }
}
