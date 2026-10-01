<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
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
                    ]),
            ]);
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
