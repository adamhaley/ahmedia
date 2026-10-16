<?php

namespace App\Filament\Resources\FaqItems;

use App\Filament\Resources\FaqItems\Pages\ManageFaqItems;
use App\Models\FaqItem;
use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class FaqItemResource extends Resource
{
    protected static ?string $model = FaqItem::class;

    protected static string|UnitEnum|null $navigationGroup = 'Site';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQuestionMarkCircle;

    protected static ?string $modelLabel = 'FAQ';

    protected static ?string $pluralModelLabel = 'FAQs';

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            TextInput::make('question')->required(),
            RichEditor::make('answer')
                ->required()
                ->toolbarButtons([['bold', 'italic', 'link'], ['bulletList', 'orderedList'], ['undo', 'redo']]),
            TagsInput::make('keywords')
                ->separator(',')
                ->label('Chatbot keywords')
                ->helperText('Words a visitor might type instead of your wording — e.g. price, budget, how much. These weigh heavily when the chatbot picks an answer.'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('question')->wrap()->searchable(),
            ])
            ->headerActions([
                CreateAction::make()->slideOver(),
            ])
            ->recordActions([
                EditAction::make()->slideOver(),
                DeleteAction::make(),
            ])
            ->recordAction('edit');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageFaqItems::route('/'),
        ];
    }
}
