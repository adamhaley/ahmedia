<?php

namespace Tests\Database\Migrations;

use App\Enums\PageTemplate;
use App\Models\FaqItem;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RewordDemoReceptionistCopyTest extends TestCase
{
    use RefreshDatabase;

    private const OLD_BODY = 'Call our demo receptionist at **[(765) 307-5537](tel:+17653075537)**. She answers for a sample med spa and can book you a consultation. Yours would be trained on your own business.';

    private function runMigration(): void
    {
        foreach (glob(database_path('migrations/*_reword_demo_receptionist_copy.php')) as $file) {
            (require $file)->up();
        }
    }

    private function createVoicePage(string $body = self::OLD_BODY): Page
    {
        $services = Page::query()->create(['title' => 'Services', 'slug' => 'services', 'template' => PageTemplate::Service, 'is_published' => true]);

        return Page::query()->create([
            'parent_id' => $services->id,
            'title' => 'Voice AI Receptionists',
            'slug' => 'voice-ai-receptionists',
            'template' => PageTemplate::Service,
            'is_published' => true,
            'hero_subheading' => 'Never miss a call. Call the demo: (765) 307-5537.',
            'body' => $body,
            'side_card' => ['title' => 'Hear it', 'note' => 'Call our Demo Receptionist any time.'],
        ]);
    }

    #[Test]
    public function it_rewords_all_five_fields(): void
    {
        $home = Page::query()->create([
            'title' => 'AH Media',
            'template' => PageTemplate::Home,
            'is_published' => true,
            'hero_points' => ['Demo receptionist: [(765) 307-5537](tel:+17653075537)', 'Other point'],
        ]);
        $voice = $this->createVoicePage();
        $faq = FaqItem::factory()->create([
            'question' => 'What is an AI voice receptionist?',
            'answer' => '<p>Hear it: call our demo receptionist at <a href="tel:+17653075537">(765) 307-5537</a>.</p>',
            'keywords' => 'demo receptionist',
        ]);

        $this->runMigration();

        $home->refresh();
        $voice->refresh();
        $faq->refresh();

        $this->assertSame(['Call our AI receptionist: [(765) 307-5537](tel:+17653075537)', 'Other point'], $home->hero_points);
        $this->assertSame('Never miss a call. Call our AI receptionist: (765) 307-5537.', $voice->hero_subheading);
        $this->assertSame('Call our AI receptionist at **[(765) 307-5537](tel:+17653075537)**. Yours would be trained on your own business.', $voice->body);
        $this->assertSame('Call our AI receptionist any time.', $voice->side_card['note']);
        $this->assertStringContainsString('call our AI receptionist at', $faq->answer);
        $this->assertSame('demo receptionist', $faq->keywords);

        foreach ([implode(' ', $home->hero_points), $voice->hero_subheading, $voice->body, $voice->side_card['note'], $faq->answer] as $text) {
            $this->assertStringNotContainsStringIgnoringCase('demo', $text);
        }
    }

    #[Test]
    public function it_leaves_a_body_edited_in_the_admin_unchanged(): void
    {
        $voice = $this->createVoicePage('Our own words about the demo receptionist.');

        $this->runMigration();

        $this->assertSame('Our own words about the demo receptionist.', $voice->refresh()->body);
    }

    #[Test]
    public function it_runs_cleanly_when_the_pages_and_faq_do_not_exist(): void
    {
        $this->runMigration();

        $this->assertSame(0, Page::query()->count());
        $this->assertSame(0, FaqItem::query()->count());
    }
}
