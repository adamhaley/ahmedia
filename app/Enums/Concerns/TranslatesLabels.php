<?php

declare(strict_types=1);

namespace App\Enums\Concerns;

trait TranslatesLabels
{
    public function getLabel(): string
    {
        return trans('enums.'.static::class.'.'.$this->value);
    }
}
