<?php

namespace Tests\Filament\Resources\Pages\Pages;

use App\Enums\PageTemplate;
use App\Filament\Resources\Pages\Pages\EditPage;
use App\Models\AuthorizedEmail;
use App\Models\DopplerRingStyle;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(EditPage::class)]
class EditPageTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_turns_rings_off_for_a_child_page(): void
    {
        $page = $this->childOfPageWithRings();

        Livewire::actingAs($this->admin())
            ->test(EditPage::class, ['record' => $page->getKey()])
            ->fillForm(['doppler_ring_style_id' => 'none'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame([true, null], [$page->fresh()->hide_doppler_rings, $page->fresh()->doppler_ring_style_id]);
    }

    #[Test]
    public function it_shows_no_rings_as_chosen_when_editing_a_page_with_rings_off(): void
    {
        $page = $this->childOfPageWithRings();
        $page->update(['hide_doppler_rings' => true]);

        $component = Livewire::actingAs($this->admin())->test(EditPage::class, ['record' => $page->getKey()]);

        $component->assertSchemaStateSet(['doppler_ring_style_id' => 'none']);
    }

    #[Test]
    public function it_overrides_the_parents_rings_with_a_style(): void
    {
        $page = $this->childOfPageWithRings();
        $page->update(['hide_doppler_rings' => true]);
        $style = DopplerRingStyle::factory()->create();

        Livewire::actingAs($this->admin())
            ->test(EditPage::class, ['record' => $page->getKey()])
            ->fillForm(['doppler_ring_style_id' => $style->id])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame([false, $style->id], [$page->fresh()->hide_doppler_rings, $page->fresh()->doppler_ring_style_id]);
    }

    private function childOfPageWithRings(): Page
    {
        $parent = Page::query()->create(['title' => 'Services', 'slug' => 'services', 'template' => PageTemplate::Standard, 'doppler_ring_style_id' => DopplerRingStyle::factory()->create()->id]);

        return Page::query()->create(['title' => 'Websites', 'slug' => 'websites', 'template' => PageTemplate::Standard, 'parent_id' => $parent->id]);
    }

    private function admin(): User
    {
        $user = User::factory()->create();
        AuthorizedEmail::query()->create(['email' => $user->email, 'is_active' => true]);

        return $user;
    }
}
