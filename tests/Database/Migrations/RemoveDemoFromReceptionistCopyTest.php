<?php

namespace Tests\Database\Migrations;

use App\Enums\PageTemplate;
use App\Models\FaqItem;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RemoveDemoFromReceptionistCopyTest extends TestCase
{
    use RefreshDatabase;

    private function runMigration(): void
    {
        foreach (glob(database_path('migrations/*_remove_demo_from_receptionist_copy.php')) as $file) {
            (require $file)->up();
        }
    }

    #[Test]
    public function it_replaces_demo_receptionist_everywhere(): void
    {
        $voice = Page::query()->create([
            'title' => 'Voice',
            'slug' => 'voice',
            'template' => PageTemplate::Service,
            'is_published' => true,
            'hero_subheading' => 'Call our DEMO receptionist now. Never miss a call.',
            'body' => 'Try the Demo Receptionist today.',
            'bullets' => ['Meet the demo receptionist', 'Unrelated bullet'],
            'side_card' => [
                'title' => 'Demo Receptionist',
                'link_label' => 'Call the demo receptionist',
                'link_url' => 'tel:+17653075537',
                'note' => 'Our demo receptionist answers 24/7.',
            ],
        ]);
        $home = Page::query()->create([
            'title' => 'AH Media',
            'template' => PageTemplate::Home,
            'is_published' => true,
            'hero_points' => ['Demo receptionist: [(765) 307-5537](tel:+17653075537)', 'Other point'],
        ]);
        $faq = FaqItem::factory()->create([
            'answer' => '<p>Call our demo receptionist at 555.</p>',
            'keywords' => 'demo receptionist, try demo',
        ]);

        $this->runMigration();

        $voice->refresh();
        $home->refresh();
        $faq->refresh();

        $this->assertSame('Call our 24/7 AI Receptionist now. Never miss a call.', $voice->hero_subheading);
        $this->assertSame('Try the 24/7 AI Receptionist today.', $voice->body);
        $this->assertSame(['Meet the 24/7 AI Receptionist', 'Unrelated bullet'], $voice->bullets);
        $this->assertSame('24/7 AI Receptionist', $voice->side_card['title']);
        $this->assertSame('Call the 24/7 AI Receptionist', $voice->side_card['link_label']);
        $this->assertSame('Our 24/7 AI Receptionist answers 24/7.', $voice->side_card['note']);
        $this->assertSame('tel:+17653075537', $voice->side_card['link_url']);
        $this->assertSame('24/7 AI Receptionist: [(765) 307-5537](tel:+17653075537)', $home->hero_points[0]);
        $this->assertSame('Other point', $home->hero_points[1]);
        $this->assertSame('<p>Call our 24/7 AI Receptionist at 555.</p>', $faq->answer);
        $this->assertSame('demo receptionist, try demo', $faq->keywords);

        $visible = json_encode([$voice->only(['hero_subheading', 'body', 'bullets', 'side_card']), $home->hero_points, $faq->answer]);
        $this->assertStringNotContainsStringIgnoringCase('demo', $visible);
    }

    #[Test]
    public function it_runs_cleanly_on_an_empty_database(): void
    {
        $this->runMigration();

        $this->assertSame(0, Page::query()->count());
        $this->assertSame(0, FaqItem::query()->count());
    }
}
