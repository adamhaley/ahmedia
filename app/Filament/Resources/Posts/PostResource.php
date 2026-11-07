<?php

namespace App\Filament\Resources\Posts;

use App\Filament\Resources\Posts\Pages\CreatePost;
use App\Filament\Resources\Posts\Pages\EditPost;
use App\Filament\Resources\Posts\Pages\ListPosts;
use App\Models\Post;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static string|UnitEnum|null $navigationGroup = 'Site';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 15;

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make('Post')->columns(2)->schema([
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
                    ->helperText(fn (?string $state): string => 'Public URL: /blog/'.($state ?: 'slug')),
                DateTimePicker::make('published_at')
                    ->label('Publish on')
                    ->helperText('Empty keeps it a draft; a future date schedules it.'),
                FileUpload::make('cover_image')
                    ->image()
                    ->imageEditor()
                    ->disk('public')
                    ->directory('posts')
                    ->visibility('public'),
                Textarea::make('excerpt')
                    ->helperText('Shown on the blog index and under the title.')
                    ->rows(2)
                    ->maxLength(500)
                    ->columnSpanFull(),
            ]),
            Section::make('Content')->schema([
                MarkdownEditor::make('body')
                    ->hiddenLabel()
                    ->fileAttachmentsDisk('public')
                    ->fileAttachmentsDirectory('posts'),
            ]),
            Section::make('Search engines')->collapsed()->schema([
                Textarea::make('meta_description')->rows(2)->maxLength(500)->placeholder('Defaults to the excerpt'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('published_at', 'desc')
            ->columns([
                TextColumn::make('title')->description(fn (Post $record): string => $record->url())->searchable(),
                TextColumn::make('published_at')
                    ->label('Published')
                    ->dateTime('M j, Y')
                    ->placeholder('Draft')
                    ->badge()
                    ->color(fn (Post $record): string => $record->isPublished() ? 'success' : 'gray')
                    ->sortable(),
                TextColumn::make('updated_at')->since()->sortable(),
            ])
            ->recordActions([
                Action::make('viewLive')
                    ->label('View')
                    ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                    ->color('gray')
                    ->url(fn (Post $record): string => $record->url(), shouldOpenInNewTab: true),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->recordAction('edit');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPosts::route('/'),
            'create' => CreatePost::route('/create'),
            'edit' => EditPost::route('/{record}/edit'),
        ];
    }
}
