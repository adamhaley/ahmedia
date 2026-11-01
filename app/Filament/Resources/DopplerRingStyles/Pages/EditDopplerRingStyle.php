<?php

namespace App\Filament\Resources\DopplerRingStyles\Pages;

use App\Filament\Resources\DopplerRingStyles\DopplerRingStyleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDopplerRingStyle extends EditRecord
{
    protected static string $resource = DopplerRingStyleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
