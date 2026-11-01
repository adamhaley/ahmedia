<?php

namespace App\Filament\Resources\DopplerRingStyles;

use App\Data\DopplerRingSettings;
use App\Filament\Resources\DopplerRingStyles\Pages\CreateDopplerRingStyle;
use App\Filament\Resources\DopplerRingStyles\Pages\EditDopplerRingStyle;
use App\Filament\Resources\DopplerRingStyles\Pages\ListDopplerRingStyles;
use App\Models\DopplerRingStyle;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class DopplerRingStyleResource extends Resource
{
    protected static ?string $model = DopplerRingStyle::class;

    protected static string|UnitEnum|null $navigationGroup = 'Site';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSignal;

    protected static ?string $navigationLabel = 'Doppler rings';

    protected static ?string $modelLabel = 'rings style';

    protected static ?int $navigationSort = 40;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->required()
                ->maxLength(255)
                ->helperText('Pages pick a rings style by this name, in their Hero section.'),
            // The Logo story lab, as an editor: its sliders send their values here (public/doppler-rings.js)
            ViewField::make('settings')
                ->view('filament.doppler-rings-editor')
                ->hiddenLabel()
                ->formatStateUsing(fn (mixed $state): array => ($state instanceof DopplerRingSettings ? $state : DopplerRingSettings::defaults())->toArray())
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('pages_count')->counts('pages')->label('Used by pages'),
                TextColumn::make('updated_at')->label('Updated')->since(),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDopplerRingStyles::route('/'),
            'create' => CreateDopplerRingStyle::route('/create'),
            'edit' => EditDopplerRingStyle::route('/{record}/edit'),
        ];
    }
}
