<?php

namespace App\Filament\Resources\Testimonials;

use App\Filament\Resources\Testimonials\Pages\ManageTestimonials;
use App\Models\Testimonial;
use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use UnitEnum;

class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;

    protected static string|UnitEnum|null $navigationGroup = 'Site';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleBottomCenterText;

    protected static ?int $navigationSort = 25;

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Textarea::make('body')
                ->label('Quote')
                ->required()
                ->rows(4)
                ->columnSpanFull()
                ->helperText('Without surrounding quotation marks; the site adds them.'),
            TextInput::make('name')->required()->placeholder('e.g. Ronnie A.'),
            TextInput::make('affiliation')->label('Role or company')->placeholder('e.g. Director'),
            TextInput::make('project')->placeholder('e.g. n8n workflow automation'),
            Select::make('rating')
                ->options([5 => '5 stars', 4 => '4 stars', 3 => '3 stars', 2 => '2 stars', 1 => '1 star'])
                ->placeholder('No rating (hide stars)'),
            Toggle::make('is_active')->label('Show on the homepage')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('body')->label('Quote')->limit(80)->wrap()->searchable(),
                TextColumn::make('name')->description(fn (Testimonial $record): ?string => $record->project)->searchable(),
                TextColumn::make('rating')->suffix(' ★')->placeholder('—'),
                ToggleColumn::make('is_active')->label('On homepage'),
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
            'index' => ManageTestimonials::route('/'),
        ];
    }
}
