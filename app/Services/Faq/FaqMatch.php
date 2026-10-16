<?php

declare(strict_types=1);

namespace App\Services\Faq;

use App\Models\FaqItem;

readonly class FaqMatch
{
    public function __construct(
        public FaqItem $faq,
        public float $score,
    ) {}
}
