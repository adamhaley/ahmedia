<?php

namespace Tests\Filament\Resources\DopplerRingStyles\Pages;

use App\Filament\Resources\DopplerRingStyles\Pages\EditDopplerRingStyle;
use App\Models\AuthorizedEmail;
use App\Models\DopplerRingStyle;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(EditDopplerRingStyle::class)]
class EditDopplerRingStyleTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_renders_the_lab_as_the_editor(): void
    {
        $style = DopplerRingStyle::factory()->create();

        $component = Livewire::actingAs($this->admin())->test(EditDopplerRingStyle::class, ['record' => $style->getKey()]);

        $component->assertSeeHtml('data-doppler-editor');
    }

    #[Test]
    public function it_saves_the_settings_sent_by_the_editor(): void
    {
        $style = DopplerRingStyle::factory()->create();

        Livewire::actingAs($this->admin())
            ->test(EditDopplerRingStyle::class, ['record' => $style->getKey()])
            ->set('data.settings', [...$style->settings->toArray(), 'mach' => 1.5, 'seed' => 42])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame([1.5, 42], [$style->fresh()->settings->mach, $style->fresh()->settings->seed]);
    }

    private function admin(): User
    {
        $user = User::factory()->create();
        AuthorizedEmail::query()->create(['email' => $user->email, 'is_active' => true]);

        return $user;
    }
}
