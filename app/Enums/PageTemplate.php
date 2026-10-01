<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Concerns\TranslatesLabels;
use Filament\Support\Contracts\HasLabel;

enum PageTemplate: string implements HasLabel
{
    use TranslatesLabels;

    case Home = 'home';
    case Contact = 'contact';
    case ServicesIndex = 'services_index';
    case Service = 'service';
    case Standard = 'standard';

    // Templates the site structure depends on; only freely creatable/deletable
    // templates (Service, Standard) may be applied or removed without care.
    public function isSystem(): bool
    {
        return ! in_array($this, [self::Service, self::Standard], true);
    }

    public function view(): string
    {
        return 'pages.'.$this->value;
    }
}
