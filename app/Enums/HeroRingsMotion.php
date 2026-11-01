<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\TranslatesLabels;
use Filament\Support\Contracts\HasLabel;

enum HeroRingsMotion: string implements HasLabel
{
    use TranslatesLabels;

    case Scroll = 'scroll';
    case Auto = 'auto';
    case Still = 'still';
}
