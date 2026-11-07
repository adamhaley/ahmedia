<?php

namespace App\Filament\Resources\Projects;

use App\Enums\PageTemplate;
use App\Enums\ProjectType;
use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Filament\Resources\Projects\Pages\EditProject;
use App\Filament\Resources\Projects\Pages\ListProjects;
use App\Filament\Resources\Projects\RelationManagers\ImagesRelationManager;
use App\Models\Project;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use UnitEnum;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static string|UnitEnum|null $navigationGroup = 'Site';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 21;

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make('Project')->columns(2)->schema([
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
                    ->helperText('Anchor on its page, e.g. /services/automations/#slug'),
                Select::make('type')
                    ->options(ProjectType::class)
                    ->required()
                    ->helperText('Projects are grouped under this heading on the page.'),
                Select::make('page_id')
                    ->label('Show on page')
                    ->relationship('page', 'title', fn (Builder $query) => $query->where('template', PageTemplate::Service))
                    ->preload()
                    ->placeholder('Not shown anywhere yet'),
                TagsInput::make('tags'),
                Select::make('case_study_id')
                    ->label('Related case study')
                    ->relationship('caseStudy', 'title')
                    ->preload()
                    ->placeholder('None')
                    ->helperText('Adds a "Read the case study" link.'),
                Toggle::make('is_published')->default(true),
            ]),

            Section::make('Content')->schema([
                Textarea::make('description')
                    ->helperText('Markdown supported: **bold**, lists, links.')
                    ->rows(5),
                FileUpload::make('cover_image')
                    ->image()
                    ->imageEditor()
                    ->disk('public')
                    ->directory('projects')
                    ->visibility('public')
                    ->helperText('The first slide. Add more slides in the Gallery tab below once saved.'),
            ]),

            Section::make('Get it')
                ->description('Shown as buttons on the card. The first one is the primary button.')
                ->schema([
                    FileUpload::make('download_file')
                        ->label('Free download')
                        ->disk('public')
                        ->directory('projects/downloads')
                        ->visibility('public')
                        ->preserveFilenames()
                        ->helperText('E.g. an n8n workflow .json. Shown as a "Download free" button.'),
                    Repeater::make('links')
                        ->schema([
                            TextInput::make('label')->required()->placeholder('Buy on Gumroad'),
                            TextInput::make('url')->label('URL')->required()->url(),
                        ])
                        ->columns(2)
                        ->reorderable()
                        ->defaultItems(0)
                        ->addActionLabel('Add link'),
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
                TextColumn::make('type')->badge(),
                TextColumn::make('page.title')->label('Page')->placeholder('—'),
                IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean()
                    ->falseIcon(Heroicon::OutlinedMinusCircle)
                    ->falseColor('gray'),
                TextColumn::make('updated_at')->since()->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')->options(ProjectType::class),
                SelectFilter::make('page_id')->label('Page')->relationship('page', 'title'),
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
            'index' => ListProjects::route('/'),
            'create' => CreateProject::route('/create'),
            'edit' => EditProject::route('/{record}/edit'),
        ];
    }
}
