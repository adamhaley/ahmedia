<?php

namespace App\Filament\Resources\CaseStudies;

use App\Filament\Resources\CaseStudies\Pages\CreateCaseStudy;
use App\Filament\Resources\CaseStudies\Pages\EditCaseStudy;
use App\Filament\Resources\CaseStudies\Pages\ListCaseStudies;
use App\Filament\Resources\CaseStudies\RelationManagers\ImagesRelationManager;
use App\Models\CaseStudy;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;

class CaseStudyResource extends Resource
{
    protected static ?string $model = CaseStudy::class;

    protected static string|UnitEnum|null $navigationGroup = 'Site';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make('Case study')->columns(2)->schema([
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
                    ->unique(ignoreRecord: true)
                    ->helperText('Not used by a public page yet -- reserved for a future case study detail page.'),
                TextInput::make('client_name'),
                TextInput::make('external_link')->url(),
                DatePicker::make('started_at'),
                DatePicker::make('completed_at'),
                TagsInput::make('tags'),
                Toggle::make('is_published')->default(true),
            ]),

            Section::make('Content')->schema([
                Textarea::make('summary')
                    ->label('Summary')
                    ->helperText('Short card-grid excerpt.')
                    ->rows(2)
                    ->maxLength(500),
                Textarea::make('narrative')
                    ->label('Narrative')
                    ->helperText('Longer client-facing explanation of what was built and why. Not yet rendered anywhere public -- reserved for a future case study detail page.')
                    ->rows(6),
                FileUpload::make('hero_image')
                    ->image()
                    ->imageEditor()
                    ->disk('public')
                    ->directory('case-studies')
                    ->visibility('public'),
            ]),

            Section::make('Testimonial')
                ->description('Leave empty if this case study has no testimonial.')
                ->columns(2)
                ->schema([
                    Textarea::make('testimonial_quote')->columnSpanFull()->rows(3),
                    TextInput::make('testimonial_author'),
                    TextInput::make('testimonial_author_role')->label('Author role'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('title')->searchable(),
                TextColumn::make('client_name')->placeholder('—'),
                IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean()
                    ->falseIcon(Heroicon::OutlinedMinusCircle)
                    ->falseColor('gray'),
                TextColumn::make('updated_at')->since()->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_published')->label('Published'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->recordAction('edit');
    }

    public static function getRelations(): array
    {
        return [
            ImagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCaseStudies::route('/'),
            'create' => CreateCaseStudy::route('/create'),
            'edit' => EditCaseStudy::route('/{record}/edit'),
        ];
    }
}
