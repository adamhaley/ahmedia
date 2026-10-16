<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\TranslatesLabels;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum FaqMatchOutcome: string implements HasColor, HasLabel
{
    use TranslatesLabels;

    /** One FAQ scored well and clearly beat the runner-up -- answer it directly. */
    case Confident = 'confident';

    /** Several FAQs scored close together -- offer them rather than guess. */
    case Ambiguous = 'ambiguous';

    /** Nothing scored well enough to show -- hand off to a human. */
    case NoMatch = 'no_match';

    public function getColor(): string
    {
        return match ($this) {
            self::Confident => 'success',
            self::Ambiguous => 'warning',
            self::NoMatch => 'danger',
        };
    }
}
