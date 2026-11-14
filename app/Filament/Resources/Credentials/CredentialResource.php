<?php

namespace App\Filament\Resources\Credentials;

use App\Enums\PageTemplate;
use App\Filament\Resources\Credentials\Pages\ManageCredentials;
use App\Models\Credential;
use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
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
            TextInput::make('value')
                ->label('Stat')
                ->placeholder('e.g. 100%')
                ->helperText('The big figure on a featured tile. Leave empty to show the name large instead.'),
            DatePicker::make('issued_on')->label('Issued'),
            TextInput::make('url')->label('Verification link')->url(),
            FileUpload::make('badge_image')
                ->label('Badge')
                ->image()
                ->disk('public')
                ->directory('credentials')
                ->visibility('public'),
            Toggle::make('is_featured')
                ->label('Featured')
                ->helperText('Shown as a big stat tile in the About page\'s Track record band, not in the badge grid.'),
            Toggle::make('is_active')
                ->label('Show on the site')
                ->default(true)
                ->helperText('On the About page, and on the service page below if one is set.'),
            Select::make('page_id')
                ->label('Also show on')
                ->relationship('page', 'title', fn (Builder $query) => $query->where('template', PageTemplate::Service))
                ->preload()
                ->placeholder('Only the About page')
                ->helperText('A Certifications section on that service page. Featured stats stay on About.'),
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
                TextColumn::make('value')->label('Stat')->placeholder('—'),
                ToggleColumn::make('is_featured')->label('Featured'),
                TextColumn::make('issued_on')->label('Issued')->date('M Y'),
                TextColumn::make('page.title')->label('Also on')->placeholder('—'),
                ToggleColumn::make('is_active')->label('Shown'),
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
