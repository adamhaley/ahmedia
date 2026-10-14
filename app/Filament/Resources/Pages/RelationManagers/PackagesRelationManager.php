<?php

namespace App\Filament\Resources\Pages\RelationManagers;

use App\Enums\PageTemplate;
use App\Models\Page;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PackagesRelationManager extends RelationManager
{
    protected static string $relationship = 'packages';

    protected static ?string $title = 'Packages';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord instanceof Page && $ownerRecord->template === PageTemplate::Service;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            TextInput::make('title')
                ->required()
                ->live(onBlur: true)
                ->afterStateUpdated(function (Set $set, ?string $state, string $operation): void {
                    if ($operation === 'create') {
                        $set('slug', Str::slug((string) $state));
                    }
                }),
            TextInput::make('slug')
                ->required()
                ->alphaDash()
                ->helperText('Anchor on the service page, e.g. /services/website-design/#brand-audit'),
            TextInput::make('price')->required()->numeric()->minValue(0)->prefix('$'),
            TextInput::make('delivery_days')->numeric()->minValue(1)->suffix('days'),
            Textarea::make('summary')->columnSpanFull()->rows(2)->maxLength(500),
            Repeater::make('includes')
                ->label("What's included")
                ->simple(TextInput::make('item')->required())
                ->reorderable()
                ->defaultItems(0)
                ->columnSpanFull(),
            Repeater::make('add_ons')
                ->label('Add-ons')
                ->schema([
                    TextInput::make('title')->required(),
                    TextInput::make('price')->required()->numeric()->minValue(0)->prefix('+$'),
                    TextInput::make('days')->numeric()->minValue(0)->prefix('+')->suffix('days'),
                    Textarea::make('description')->rows(2)->columnSpanFull(),
                ])
                ->columns(3)
                ->reorderable()
                ->defaultItems(0)
                ->columnSpanFull(),
            FileUpload::make('sample_document')
                ->label('Sample report (PDF)')
                ->acceptedFileTypes(['application/pdf'])
                ->disk('public')
                ->directory('packages')
                ->visibility('public'),
            TextInput::make('upwork_url')->label('Upwork listing URL')->url(),
            Toggle::make('is_published')->default(true),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('price')->money('USD', divideBy: 1),
                TextColumn::make('delivery_days')->suffix(' days')->placeholder('—'),
                IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean()
                    ->falseIcon(Heroicon::OutlinedMinusCircle)
                    ->falseColor('gray'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
