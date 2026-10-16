<?php

namespace Tests\Services\Faq;

use App\Enums\FaqMatchOutcome;
use App\Services\Faq\FaqMatchingService;
use Database\Seeders\FaqContentSeeder;
use Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * The tuning harness for the chatbot, run against the real seeded AH Media
 * FAQs (database/seeders/data/faqs.php). When a keyword list or a matching
 * threshold changes, this is the test that says whether real visitor
 * phrasing still lands on the right answer.
 */
#[CoversNothing]
class FaqCorpusMatchingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(FaqContentSeeder::class);
    }

    #[Test]
    #[DataProvider('phrasingProvider')]
    public function it_matches_real_visitor_phrasings_to_the_right_faq(string $phrasing, string $expectedQuestion): void
    {
        $result = app(FaqMatchingService::class)->match($phrasing);

        $this->assertContains(
            $result->outcome,
            [FaqMatchOutcome::Confident, FaqMatchOutcome::Ambiguous],
            "\"{$phrasing}\" should match or at least suggest an FAQ, got {$result->outcome->value}",
        );

        $matchedQuestions = array_map(fn ($match): string => $match->faq->question, $result->matches);

        $this->assertContains(
            $expectedQuestion,
            $matchedQuestions,
            "\"{$phrasing}\" should match \"{$expectedQuestion}\", got: ".implode(' | ', $matchedQuestions),
        );
    }

    public static function phrasingProvider(): Generator
    {
        yield 'services' => ['what services do you offer', 'What does AH Media do?'];
        yield 'who runs it' => ['who is adam', 'Who is behind AH Media?'];
        yield 'hourly rate' => ['whats your hourly rate', 'How much do you charge?'];
        yield 'pricing casual' => ['how much do you charge', 'How much do you charge?'];
        yield 'retainer' => ['do you do monthly retainers', 'Do you offer monthly retainers?'];
        yield 'website cost' => ['how much for a website', 'How much does a website cost?'];
        yield 'online store' => ['can you build an online store', 'How much does a website cost?'];
        yield 'chatbot' => ['can you make a chatbot for my company', 'Can you build a chatbot for my business?'];
        yield 'voice' => ['do you have an ai phone answering service', 'What is an AI voice receptionist?'];
        yield 'automation' => ['can you automate my workflows with n8n', 'What can you automate?'];
        yield 'zapier' => ['we use zapier, can you help', 'What can you automate?'];
        yield 'brand audit' => ['my site looks generic like every ai site', 'What is the Brand Audit for AI-built sites?'];
        yield 'fonts' => ['help with fonts and colours', 'What is the Brand Audit for AI-built sites?'];
        yield 'vibe coded' => ['I vibe coded an app with lovable, is it safe to launch', 'What is the Vibe-Coded App Go-Live Review?'];
        yield 'supabase' => ['security review for my supabase app', 'What is the Vibe-Coded App Go-Live Review?'];
        yield 'hacked' => ['my wordpress site got hacked', 'My website has been hacked. Can you help?'];
        yield 'malware typo' => ['there is malwear on my site', 'My website has been hacked. Can you help?'];
        yield 'red warning' => ['chrome shows a red deceptive site warning', 'My website has been hacked. Can you help?'];
        yield 'stack' => ['what tech stack do you use', 'What technology do you work with?'];
        yield 'laravel' => ['do you work with laravel', 'What technology do you work with?'];
        yield 'not technical' => ["i'm not very technical", "I'm not technical. Can I still work with you?"];
        yield 'hosting' => ['can you host my site', 'Can you set up hosting for me?'];
        yield 'domain' => ['help with my domain and ssl', 'Can you set up hosting for me?'];
        yield 'getting started' => ['how do we get started', 'How do projects start?'];
        yield 'timeline' => ['how long does it take', 'How long does a project take?'];
    }
}
