<?php

namespace App\Filament\Resources\Credentials;

use App\Filament\Resources\Credentials\Pages\ManageCredentials;
use App\Models\Credential;
use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use UnitEnum;

class CredentialResource extends Resource
{
    protected static ?string $model = Credential::class;

    protected static string|UnitEnum|null $navigationGroup = 'Site';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCheckBadge;

    protected static ?int $navigationSort = 26;

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            TextInput::make('name')->required()->placeholder('e.g. N8N101 Essentials: Your First Workflows'),
            TextInput::make('issuer')->required()->placeholder('e.g. n8n'),
            DatePicker::make('issued_on')->label('Issued'),
            TextInput::make('url')->label('Verification link')->url(),
            FileUpload::make('badge_image')
                ->label('Badge')
                ->image()
                ->disk('public')
                ->directory('credentials')
                ->visibility('public'),
            Toggle::make('is_active')->label('Show on the About page')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                ImageColumn::make('badge_image')->label('Badge')->disk('public'),
                TextColumn::make('name')->description(fn (Credential $record): string => $record->issuer)->searchable(),
                TextColumn::make('issued_on')->label('Issued')->date('M Y'),
                ToggleColumn::make('is_active')->label('On About page'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->recordAction('edit');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCredentials::route('/'),
        ];
    }
}
