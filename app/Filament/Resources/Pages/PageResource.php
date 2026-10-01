<?php

namespace App\Filament\Resources\Pages;

use App\Enums\ButtonStyle;
use App\Enums\PageTemplate;
use App\Filament\Resources\Pages\Pages\CreatePage;
use App\Filament\Resources\Pages\Pages\EditPage;
use App\Filament\Resources\Pages\Pages\ListPages;
use App\Filament\Resources\Pages\RelationManagers\ChildPagesRelationManager;
use App\Models\Page;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Unique;
use UnitEnum;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static string|UnitEnum|null $navigationGroup = 'Site';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make('Page')->columns(2)->schema([
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
                    ->prefix(fn (Get $get): string => Page::query()->find($get('parent_id'))?->url() ?? '/')
                    ->unique(ignoreRecord: true, modifyRuleUsing: fn (Unique $rule, Get $get): Unique => $rule->where('parent_id', $get('parent_id')))
                    ->hidden(fn (Get $get): bool => self::template($get) === PageTemplate::Home),
                Select::make('parent_id')
                    ->label('Parent page')
                    ->options(fn (?Page $record): array => Page::query()
                        ->whereNotIn('id', $record ? [$record->id, ...$record->descendantIds()] : [])
                        ->where('template', '!=', PageTemplate::Home)
                        ->orderBy('path')
                        ->pluck('title', 'id')
                        ->all())
                    ->placeholder('None (top level)')
                    ->live()
                    ->hidden(fn (Get $get): bool => self::template($get) === PageTemplate::Home),
                Select::make('template')
                    ->options(fn (?Page $record): array => self::templateOptions($record))
                    ->default(PageTemplate::Standard)
                    ->required()
                    ->live(),
                Toggle::make('is_published')
                    ->label('Published')
                    ->default(true)
                    ->helperText('Unpublished pages are only visible while logged in to the admin.'),
                Toggle::make('show_in_nav')
                    ->label('Show in main navigation')
                    ->helperText('Top-level pages only; child pages appear in their parent\'s dropdown.'),
            ]),

            Section::make('Hero')->schema([
                TextInput::make('hero_heading')->label('Heading')->placeholder('Defaults to the page title'),
                TextInput::make('hero_subheading')->label('Subheading'),
                FileUpload::make('hero_image')
                    ->label('Hero image')
                    ->helperText('Not currently used by any ahmedia template (the current hero is a text/glow treatment) -- kept for future use.')
                    ->image()
                    ->imageEditor()
                    ->disk('public')
                    ->directory('pages')
                    ->visibility('public')
                    ->hidden(fn (Get $get): bool => self::template($get) === PageTemplate::Home),
            ]),

            Section::make('Content')
                ->hidden(fn (Get $get): bool => self::template($get) === PageTemplate::Home)
                ->schema([
                    TextInput::make('section_eyebrow')->label('Eyebrow'),
                    TextInput::make('section_heading')->label('Heading'),
                    Textarea::make('body')->rows(5),
                    Grid::make(1)
                        ->visible(fn (Get $get): bool => in_array(self::template($get), [PageTemplate::Standard, PageTemplate::Service], true))
                        ->schema([
                            Repeater::make('bullets')
                                ->simple(TextInput::make('bullet')->required())
                                ->reorderable()
                                ->defaultItems(0),
                            Repeater::make('buttons')
                                ->schema([
                                    TextInput::make('label')->required(),
                                    TextInput::make('url')->label('Link')->required()->placeholder('/services/ or /contact/'),
                                    Select::make('style')->options(ButtonStyle::class)->default(ButtonStyle::Secondary->value)->required(),
                                ])
                                ->columns(3)
                                ->reorderable()
                                ->defaultItems(0),
                        ]),
                ]),

            Section::make('Services card')
                ->description('How this service appears in the grid on the homepage and the Services page.')
                ->visible(fn (Get $get): bool => self::template($get) === PageTemplate::Service)
                ->schema([
                    Textarea::make('card_excerpt')->label('Card text')->rows(2),
                    FileUpload::make('card_image')
                        ->label('Card image')
                        ->image()
                        ->imageEditor()
                        ->disk('public')
                        ->directory('pages')
                        ->visibility('public'),
                ]),

            Section::make('Call to action')
                ->description('The banner at the bottom of the page. Leave the heading empty to hide it.')
                ->hidden(fn (Get $get): bool => in_array(self::template($get), [PageTemplate::Home, PageTemplate::Contact], true))
                ->columns(3)
                ->schema([
                    TextInput::make('cta_eyebrow')->label('Eyebrow'),
                    TextInput::make('cta_heading')->label('Heading'),
                    TextInput::make('cta_button_label')->label('Button label')->placeholder('Start a Project'),
                ]),

            Section::make('Search engines')->collapsed()->schema([
                TextInput::make('meta_title')->placeholder('Defaults to "Page title | AH Media.ai"'),
                Textarea::make('meta_description')->rows(2)->maxLength(500),
                FileUpload::make('og_image')
                    ->label('Social sharing image')
                    ->image()
                    ->imageEditor()
                    ->imageEditorAspectRatios(['1200:630'])
                    ->disk('public')
                    ->directory('pages')
                    ->visibility('public'),
                TextInput::make('nav_anchor')
                    ->label('Homepage navigation anchor')
                    ->helperText('When set, this page\'s nav link jumps to that homepage section (e.g. #approach) instead of opening the page.'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('path')
            ->columns([
                TextColumn::make('title')
                    ->description(fn (Page $record): string => $record->url())
                    ->searchable(),
                TextColumn::make('template')->badge(),
                IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean()
                    ->falseIcon(Heroicon::OutlinedMinusCircle)
                    ->falseColor('gray'),
                IconColumn::make('show_in_nav')
                    ->label('In nav')
                    ->boolean()
                    ->falseIcon(Heroicon::OutlinedMinusCircle)
                    ->falseColor('gray'),
                TextColumn::make('updated_at')->since()->sortable(),
            ])
            ->filters([
                SelectFilter::make('template')->options(PageTemplate::class),
                TernaryFilter::make('is_published')->label('Published'),
            ])
            ->recordActions([
                Action::make('viewLive')
                    ->label('View')
                    ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                    ->color('gray')
                    ->url(fn (Page $record): string => $record->url(), shouldOpenInNewTab: true),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->recordAction('edit');
    }

    public static function getRelations(): array
    {
        return [
            ChildPagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPages::route('/'),
            'create' => CreatePage::route('/create'),
            'edit' => EditPage::route('/{record}/edit'),
        ];
    }

    private static function template(Get $get): ?PageTemplate
    {
        $template = $get('template');

        return $template instanceof PageTemplate ? $template : PageTemplate::tryFrom((string) $template);
    }

    /** @return array<string, string> */
    private static function templateOptions(?Page $record): array
    {
        $usedSystemTemplates = Page::query()
            ->when($record, fn ($query) => $query->whereKeyNot($record->id))
            ->pluck('template')
            ->filter(fn (PageTemplate $template): bool => $template->isSystem());

        return collect(PageTemplate::cases())
            ->filter(fn (PageTemplate $template): bool => $template === $record?->template
                || ! $template->isSystem()
                || ! $usedSystemTemplates->contains($template))
            ->mapWithKeys(fn (PageTemplate $template): array => [$template->value => $template->getLabel()])
            ->all();
    }
}
