<?php

namespace App\Filament\Resources\Pages\RelationManagers;

use App\Filament\Resources\Pages\PageResource;
use App\Models\Page;
use Filament\Actions\Action;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ChildPagesRelationManager extends RelationManager
{
    protected static string $relationship = 'children';

    protected static ?string $title = 'Child pages';

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('title')->description(fn (Page $record): string => $record->url()),
                TextColumn::make('template')->badge(),
                IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean()
                    ->falseIcon(Heroicon::OutlinedMinusCircle)
                    ->falseColor('gray'),
            ])
            ->headerActions([
                Action::make('create')
                    ->label('New child page')
                    ->url(fn (): string => PageResource::getUrl('create', ['parent' => $this->getOwnerRecord()->getKey()])),
            ])
            ->recordActions([
                Action::make('edit')
                    ->icon('heroicon-m-pencil-square')
                    ->url(fn (Page $record): string => PageResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
