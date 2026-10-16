<?php

namespace App\Filament\Resources\ContactSubmissions;

use App\Filament\Resources\ContactSubmissions\Pages\ListContactSubmissions;
use App\Filament\Resources\ContactSubmissions\Pages\ViewContactSubmission;
use App\Models\ContactSubmission;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class ContactSubmissionResource extends Resource
{
    protected static ?string $model = ContactSubmission::class;

    protected static string|UnitEnum|null $navigationGroup = 'Leads';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInbox;

    protected static ?string $navigationLabel = 'Contact form';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationBadge(): ?string
    {
        $unread = ContactSubmission::query()->whereNull('read_at')->count();

        return $unread > 0 ? (string) $unread : null;
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            TextEntry::make('name'),
            TextEntry::make('email')->copyable(),
            TextEntry::make('created_at')->label('Received')->dateTime(),
            TextEntry::make('submitted_from')->label('Page')->placeholder('—'),
            TextEntry::make('source')->badge(),
            TextEntry::make('message')->columnSpanFull()->prose(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                IconColumn::make('read_at')
                    ->label('Read')
                    ->boolean()
                    ->state(fn (ContactSubmission $record): bool => $record->read_at !== null)
                    ->falseIcon(Heroicon::OutlinedEnvelope)
                    ->falseColor('primary'),
                TextColumn::make('name')->searchable()->weight('bold'),
                TextColumn::make('source')->badge(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('message')->limit(80),
                TextColumn::make('created_at')->label('Received')->since()->sortable(),
            ])
            ->recordActions([
                ViewAction::make(),
                DeleteAction::make(),
            ])
            ->recordAction('view');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContactSubmissions::route('/'),
            'view' => ViewContactSubmission::route('/{record}'),
        ];
    }
}
