<?php

namespace App\Filament\Pages;

use App\Enums\HeroRingsMotion;
use App\Models\SiteSetting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use UnitEnum;

/**
 * @property-read Schema $form
 */
class SiteSettings extends Page
{
    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static string|BackedEnum|null $navigationIcon = null;

    protected static ?string $title = 'Site settings';

    protected static ?int $navigationSort = 1;

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(SiteSetting::current()->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Contact details')
                    ->description('Shown in the contact section and footer across the site.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('contact_email')->label('Email')->email(),
                        TextInput::make('phone')->tel(),
                    ]),
                Section::make('Branding')
                    ->description('The accent color used sitewide -- eyebrows, buttons, links, glow effects. Leave empty for the default neon cyan.')
                    ->schema([
                        ColorPicker::make('accent_color')
                            ->label('Accent color')
                            ->hex()
                            ->placeholder(SiteSetting::DefaultAccentColor)
                            ->rule('regex:/^#[0-9a-f]{6}$/i'),
                        TextInput::make('footer_tagline')
                            ->label('Footer tagline')
                            ->helperText('The line under "AH Media" in the footer. Leave empty to hide it.')
                            ->maxLength(255),
                    ]),
                Section::make('Homepage hero rings')
                    ->description('How the Doppler rings behind the homepage title move. Scroll pins the hero and plays the rings as you scroll; Auto loops them on a timer with no pin.')
                    ->columns(3)
                    ->schema([
                        ToggleButtons::make('hero_rings_motion')
                            ->label('Motion')
                            ->options(HeroRingsMotion::class)
                            ->inline()
                            ->required()
                            ->live()
                            ->columnSpanFull(),
                        TextInput::make('hero_rings_scroll_start')
                            ->label('Scroll start')
                            ->helperText('Where in the ring cycle the pin starts. 0 is the logo arrangement.')
                            ->numeric()
                            ->step(0.05)
                            ->minValue(0)
                            ->maxValue(3)
                            ->required()
                            ->visible(fn (Get $get): bool => self::isScrollMotion($get)),
                        TextInput::make('hero_rings_scroll_end')
                            ->label('Scroll end')
                            ->helperText('Where the pin releases. One whole cycle after the start ends on the same arrangement.')
                            ->numeric()
                            ->step(0.05)
                            ->minValue(0)
                            ->maxValue(3)
                            ->gt('hero_rings_scroll_start')
                            ->required()
                            ->visible(fn (Get $get): bool => self::isScrollMotion($get)),
                        TextInput::make('hero_rings_pin_length')
                            ->label('Pin length')
                            ->helperText('How tall the scroll area is. 220 gives about 1.2 screens of scrolling.')
                            ->numeric()
                            ->integer()
                            ->suffix('% of screen')
                            ->minValue(120)
                            ->maxValue(600)
                            ->required()
                            ->visible(fn (Get $get): bool => self::isScrollMotion($get)),
                    ]),
            ]);
    }

    private static function isScrollMotion(Get $get): bool
    {
        $motion = $get('hero_rings_motion');

        return ($motion instanceof HeroRingsMotion ? $motion : HeroRingsMotion::tryFrom((string) $motion)) === HeroRingsMotion::Scroll;
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('save')
                ->footer([
                    Actions::make([
                        Action::make('save')->label('Save settings')->submit('save')->keyBindings(['mod+s']),
                    ]),
                ]),
        ]);
    }

    public function save(): void
    {
        SiteSetting::current()->update($this->form->getState());

        Notification::make()->success()->title('Settings saved')->send();
    }
}
