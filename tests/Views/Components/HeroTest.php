<?php

namespace Tests\Views\Components;

use App\Enums\HeroRingsMotion;
use App\Enums\PageTemplate;
use App\Models\Page;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversNothing]
class HeroTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_draws_doppler_rings_behind_the_homepage_hero(): void
    {
        Page::query()->create(['title' => 'AH Media', 'template' => PageTemplate::Home, 'is_published' => true]);

        $response = $this->get('/');

        $response->assertOk()->assertSee('class="doppler-rings"', false);
    }

    #[Test]
    public function it_pins_the_homepage_hero_for_scroll_driven_rings(): void
    {
        Page::query()->create(['title' => 'AH Media', 'template' => PageTemplate::Home, 'is_published' => true]);

        $response = $this->get('/');

        $response->assertOk()->assertSee('data-hero-pin', false);
    }

    #[Test]
    public function it_passes_the_scroll_settings_to_the_pinned_hero(): void
    {
        Page::query()->create(['title' => 'AH Media', 'template' => PageTemplate::Home, 'is_published' => true]);
        SiteSetting::current()->update(['hero_rings_scroll_start' => 0.5, 'hero_rings_scroll_end' => 1, 'hero_rings_pin_length' => 300]);

        $response = $this->get('/');

        $response->assertOk()->assertSee('data-start="0.5" data-end="1" style="--pin-length: 300svh"', false);
    }

    #[Test]
    public function it_loops_the_rings_without_a_pin_in_auto_motion(): void
    {
        Page::query()->create(['title' => 'AH Media', 'template' => PageTemplate::Home, 'is_published' => true]);
        SiteSetting::current()->update(['hero_rings_motion' => HeroRingsMotion::Auto]);

        $response = $this->get('/');

        $response->assertOk()->assertSee('class="doppler-rings"', false)->assertDontSee('data-hero-pin', false);
    }

    #[Test]
    public function it_leaves_rings_off_by_default(): void
    {
        $html = (string) $this->blade('<x-hero title="Services" />');

        $this->assertStringNotContainsString('doppler-rings', $html);
    }
}
