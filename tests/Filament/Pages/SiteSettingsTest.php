<?php

namespace Tests\Filament\Pages;

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
    public function it_saves_the_footer_tagline(): void
    {
        Livewire::actingAs($this->admin())
            ->test(SiteSettings::class)
            ->fillForm(['footer_tagline' => 'Systems that ship.'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('Systems that ship.', SiteSetting::current()->footer_tagline);
    }

    #[Test]
    public function it_saves_the_address(): void
    {
        Livewire::actingAs($this->admin())
            ->test(SiteSettings::class)
            ->fillForm(['address' => '5161 Lankershim Blvd, North Hollywood, CA 91601'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('5161 Lankershim Blvd, North Hollywood, CA 91601', SiteSetting::current()->address);
    }

    private function admin(): User
    {
        $user = User::factory()->create();
        AuthorizedEmail::query()->create(['email' => $user->email, 'is_active' => true]);

        return $user;
    }
}
