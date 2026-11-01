<?php

namespace Tests\Models;

use App\Enums\PageTemplate;
use App\Models\DopplerRingStyle;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(Page::class)]
class PageTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_shows_its_own_rings_style(): void
    {
        $style = DopplerRingStyle::factory()->create();
        $parent = $this->page('Services', ['doppler_ring_style_id' => DopplerRingStyle::factory()->create()->id]);
        $page = $this->page('Websites', ['parent_id' => $parent->id, 'doppler_ring_style_id' => $style->id]);

        $this->assertTrue($page->heroRingStyle()?->is($style));
    }

    #[Test]
    public function it_inherits_its_parents_rings_style(): void
    {
        $style = DopplerRingStyle::factory()->create();
        $parent = $this->page('Services', ['doppler_ring_style_id' => $style->id]);
        $page = $this->page('Websites', ['parent_id' => $parent->id]);

        $this->assertTrue($page->heroRingStyle()?->is($style));
    }

    #[Test]
    public function it_inherits_through_several_levels(): void
    {
        $style = DopplerRingStyle::factory()->create();
        $grandparent = $this->page('Services', ['doppler_ring_style_id' => $style->id]);
        $parent = $this->page('Websites', ['parent_id' => $grandparent->id]);
        $page = $this->page('Launch', ['parent_id' => $parent->id]);

        $this->assertTrue($page->heroRingStyle()?->is($style));
    }

    #[Test]
    public function it_shows_no_rings_when_turned_off_despite_its_parent(): void
    {
        $parent = $this->page('Services', ['doppler_ring_style_id' => DopplerRingStyle::factory()->create()->id]);
        $page = $this->page('Websites', ['parent_id' => $parent->id, 'hide_doppler_rings' => true]);

        $this->assertNull($page->heroRingStyle());
    }

    #[Test]
    public function it_shows_no_rings_at_the_top_level_without_a_style(): void
    {
        $page = $this->page('Approach');

        $this->assertNull($page->heroRingStyle());
    }

    /** @param array<string, mixed> $attributes */
    private function page(string $title, array $attributes = []): Page
    {
        return Page::query()->create([
            'title' => $title,
            'slug' => str($title)->slug(),
            'template' => PageTemplate::Standard,
            'is_published' => true,
            ...$attributes,
        ]);
    }
}
