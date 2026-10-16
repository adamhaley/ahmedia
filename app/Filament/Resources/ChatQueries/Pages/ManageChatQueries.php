<?php

namespace App\Filament\Resources\ChatQueries\Pages;

use App\Filament\Resources\ChatQueries\ChatQueryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageChatQueries extends ManageRecords
{
    protected static string $resource = ChatQueryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
