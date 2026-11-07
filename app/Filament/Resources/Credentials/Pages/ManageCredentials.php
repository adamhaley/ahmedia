<?php

namespace App\Filament\Resources\Credentials\Pages;

use App\Filament\Resources\Credentials\CredentialResource;
use Filament\Resources\Pages\ManageRecords;

class ManageCredentials extends ManageRecords
{
    protected static string $resource = CredentialResource::class;
}
