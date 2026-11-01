<?php

namespace Tests\Filament\Pages;

use App\Enums\HeroRingsMotion;
use App\Filament\Pages\SiteSettings;
use App\Models\AuthorizedEmail;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(SiteSettings::class)]
class SiteSettingsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_saves_the_hero_rings_scroll_settings(): void
    {
        Livewire::actingAs($this->admin())
            ->test(SiteSettings::class)
            ->fillForm(['hero_rings_motion' => HeroRingsMotion::Scroll->value, 'hero_rings_scroll_start' => 0.5, 'hero_rings_scroll_end' => 1.5, 'hero_rings_pin_length' => 300])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame(
            [HeroRingsMotion::Scroll, 0.5, 1.5, 300],
            [SiteSetting::current()->hero_rings_motion, SiteSetting::current()->hero_rings_scroll_start, SiteSetting::current()->hero_rings_scroll_end, SiteSetting::current()->hero_rings_pin_length],
        );
    }

    #[Test]
    public function it_requires_the_scroll_end_to_be_after_the_start(): void
    {
        Livewire::actingAs($this->admin())
            ->test(SiteSettings::class)
            ->fillForm(['hero_rings_scroll_start' => 1, 'hero_rings_scroll_end' => 0.5])
            ->call('save')
            ->assertHasFormErrors(['hero_rings_scroll_end']);
    }

    #[Test]
    public function it_saves_the_footer_tagline(): void
    {
        Livewire::actingAs($this->admin())
            ->test(SiteSettings::class)
            ->fillForm(['footer_tagline' => 'Systems that ship.'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('Systems that ship.', SiteSetting::current()->footer_tagline);
    }

    private function admin(): User
    {
        $user = User::factory()->create();
        AuthorizedEmail::query()->create(['email' => $user->email, 'is_active' => true]);

        return $user;
    }
}
