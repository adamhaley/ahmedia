<?php

namespace App\Filament\Resources\Pages\Pages;

use App\Filament\Resources\Pages\PageResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePage extends CreateRecord
{
    protected static string $resource = PageResource::class;

    protected function afterFill(): void
    {
        if ($parentId = request()->integer('parent')) {
            $this->data['parent_id'] = $parentId;
        }
    }
}
