<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\TranslatesLabels;
use Filament\Support\Contracts\HasLabel;

enum ButtonStyle: string implements HasLabel
{
    use TranslatesLabels;

    case Primary = 'primary';
    case Secondary = 'secondary';
}
