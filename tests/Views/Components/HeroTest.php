<?php

namespace Tests\Views\Components;

use App\Enums\PageTemplate;
use App\Models\DopplerRingStyle;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversNothing]
class HeroTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_draws_doppler_rings_behind_a_page_with_a_rings_style(): void
    {
        Page::query()->create(['title' => 'Approach', 'slug' => 'approach', 'template' => PageTemplate::Standard, 'is_published' => true, 'doppler_ring_style_id' => DopplerRingStyle::factory()->create()->id]);

        $response = $this->get('/approach');

        $response->assertOk()->assertSee('<svg class="doppler-rings"', false);
    }

    #[Test]
    public function it_draws_no_rings_for_a_page_without_a_rings_style(): void
    {
        Page::query()->create(['title' => 'Approach', 'slug' => 'approach', 'template' => PageTemplate::Standard, 'is_published' => true]);

        $response = $this->get('/approach');

        $response->assertOk()->assertDontSee('<svg class="doppler-rings"', false);
    }

    #[Test]
    public function it_pins_the_hero_with_the_rings_style_scroll_settings(): void
    {
        $style = DopplerRingStyle::factory()->create(['settings' => ['scrollStart' => 0.5, 'scrollEnd' => 1, 'pinLength' => 300]]);
        Page::query()->create(['title' => 'AH Media', 'template' => PageTemplate::Home, 'is_published' => true, 'doppler_ring_style_id' => $style->id]);

        $response = $this->get('/');

        $response->assertOk()->assertSee('data-hero-pin data-start="0.5" data-end="1" style="--pin-length: 300svh"', false);
    }

    #[Test]
    public function it_loops_the_rings_without_a_pin_in_auto_motion(): void
    {
        Page::query()->create(['title' => 'AH Media', 'template' => PageTemplate::Home, 'is_published' => true, 'doppler_ring_style_id' => DopplerRingStyle::factory()->autoMotion()->create()->id]);

        $response = $this->get('/');

        $response->assertOk()->assertSee('<svg class="doppler-rings"', false)->assertDontSee('data-hero-pin', false);
    }

    #[Test]
    public function it_passes_the_rings_style_to_the_svg(): void
    {
        $style = DopplerRingStyle::factory()->create(['settings' => ['mach' => 1.5, 'axisMobile' => 350]]);
        Page::query()->create(['title' => 'Approach', 'slug' => 'approach', 'template' => PageTemplate::Standard, 'is_published' => true, 'doppler_ring_style_id' => $style->id]);

        $response = $this->get('/approach');

        $response->assertOk()->assertSee('--mach: 1.5;', false)->assertSee('--axis-mobile: 350', false);
    }
}
