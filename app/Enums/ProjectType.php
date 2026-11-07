<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\TranslatesLabels;
use Filament\Support\Contracts\HasLabel;

enum ProjectType: string implements HasLabel
{
    use TranslatesLabels;

    case Workflow = 'workflow';
    case Website = 'website';
}
