<?php

namespace Tests\Models;

use App\Data\BodySegment;
use App\Enums\PageTemplate;
use App\Models\DopplerRingStyle;
use App\Models\Page;
use Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(Page::class)]
class PageTest extends TestCase
{
    use RefreshDatabase;

    public static function bodySegmentsProvider(): Generator
    {
        yield 'no tokens' => ["Intro\n\nMore", [new BodySegment('Intro'."\n\n".'More')]];
        yield 'a token between paragraphs' => [
            "Intro\n\n[[credentials]]\n\nMore",
            [new BodySegment('Intro'), new BodySegment(section: 'credentials'), new BodySegment('More')],
        ];
        yield 'a token first keeps an empty lead segment for the heading' => [
            "[[projects:workflow]]\nAfter",
            [new BodySegment(''), new BodySegment(section: 'projects:workflow'), new BodySegment('After')],
        ];
        yield 'adjacent tokens' => [
            "[[projects]]\n[[packages]]",
            [new BodySegment(''), new BodySegment(section: 'projects'), new BodySegment(section: 'packages')],
        ];
        yield 'an unknown or inline token stays text' => [
            "[[credentails]]\n\nSee [[packages]] below",
            [new BodySegment("[[credentails]]\n\nSee [[packages]] below")],
        ];
    }

    /** @param list<BodySegment> $expected */
    #[Test]
    #[DataProvider('bodySegmentsProvider')]
    public function it_splits_its_body_at_section_tokens(string $body, array $expected): void
    {
        $page = new Page(['body' => $body]);

        $this->assertEquals($expected, $page->bodySegments());
    }

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
