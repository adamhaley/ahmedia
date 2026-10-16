<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\TranslatesLabels;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ContactSubmissionSource: string implements HasColor, HasLabel
{
    use TranslatesLabels;

    case ContactForm = 'contact_form';
    case Chat = 'chat';

    public function getColor(): string
    {
        return match ($this) {
            self::ContactForm => 'gray',
            self::Chat => 'info',
        };
    }
}
