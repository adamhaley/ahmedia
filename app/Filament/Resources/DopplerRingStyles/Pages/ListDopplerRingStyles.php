<?php

namespace App\Filament\Resources\DopplerRingStyles\Pages;

use App\Filament\Resources\DopplerRingStyles\DopplerRingStyleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDopplerRingStyles extends ListRecords
{
    protected static string $resource = DopplerRingStyleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
