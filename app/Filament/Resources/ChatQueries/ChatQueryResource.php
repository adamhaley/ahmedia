<?php

namespace App\Filament\Resources\ChatQueries;

use App\Enums\FaqMatchOutcome;
use App\Filament\Resources\ChatQueries\Pages\ManageChatQueries;
use App\Models\ChatQuery;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

/**
 * Read-only by design: this is a log of what visitors actually asked the
 * chatbot, kept so the outcome=no_match rows can inform which FAQs to add
 * or which existing ones need better keywords (see FaqMatchingService and
 * database/seeders/data/faq-keywords.php) -- not a record meant to be
 * edited in place.
 */
class ChatQueryResource extends Resource
{
    protected static ?string $model = ChatQuery::class;

    protected static string|UnitEnum|null $navigationGroup = 'Leads';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static ?string $modelLabel = 'Chat question';

    protected static ?string $pluralModelLabel = 'Chat questions';

    protected static ?int $navigationSort = 35;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->persistFiltersInSession()
            ->deferFilters(false)
            ->columns([
                TextColumn::make('question')->wrap()->searchable(),
                TextColumn::make('outcome')->badge(),
                TextColumn::make('score')->numeric(3)->placeholder('—'),
                TextColumn::make('matchedFaq.question')->label('Matched FAQ')->wrap()->placeholder('—'),
                IconColumn::make('contact_submission_id')->label('Became a lead')->boolean()->state(fn (ChatQuery $record): bool => $record->contact_submission_id !== null),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('outcome')->options(FaqMatchOutcome::class)->default(FaqMatchOutcome::NoMatch->value),
            ])
            ->recordActions([
                DeleteAction::make(),
            ])
            // Delete is the only row action here -- without this, clicking
            // anywhere on the row would trigger it by default (Filament
            // falls back to the first recordAction), which is a real
            // footgun for a log table with no undo.
            ->recordAction(null)
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageChatQueries::route('/'),
        ];
    }
}
