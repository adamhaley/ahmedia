<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\TranslatesLabels;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ContactSubmissionClassification: string implements HasColor, HasLabel
{
    use TranslatesLabels;

    case Valid = 'valid';
    case Spam = 'spam';
    case Unanswerable = 'unanswerable';
    case NeedsHuman = 'needs_human';

    public function getColor(): string
    {
        return match ($this) {
            self::Valid => 'success',
            self::Spam => 'gray',
            self::Unanswerable => 'warning',
            self::NeedsHuman => 'info',
        };
    }
}
