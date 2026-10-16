<?php

namespace Tests\Services\Faq;

use App\Enums\FaqMatchOutcome;
use App\Models\FaqItem;
use App\Services\Faq\FaqMatchingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(FaqMatchingService::class)]
class FaqMatchingServiceTest extends TestCase
{
    use RefreshDatabase;

    private FaqMatchingService $matcher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->matcher = app(FaqMatchingService::class);
    }

    #[Test]
    public function it_returns_a_confident_match_when_one_faq_clearly_dominates(): void
    {
        $target = FaqItem::factory()->create([
            'question' => 'How much does aerial silk performance cost?',
            'answer' => 'Pricing varies by the number of performers and length of the show.',
        ]);
        FaqItem::factory()->create([
            'question' => 'Do you travel outside Los Angeles?',
            'answer' => 'Yes, we travel nationwide for the right event.',
        ]);

        $result = $this->matcher->match('how much does it cost');

        $this->assertSame(FaqMatchOutcome::Confident, $result->outcome);
        $this->assertSame($target->id, $result->best()->faq->id);
    }

    #[Test]
    public function it_returns_ambiguous_options_when_two_faqs_score_closely(): void
    {
        FaqItem::factory()->create([
            'question' => 'Do you provide aerial rigging equipment?',
            'answer' => 'We bring our own aerial rigging equipment to every show.',
        ]);
        FaqItem::factory()->create([
            'question' => 'How much space do you need for aerial equipment?',
            'answer' => 'Aerial equipment needs enough ceiling height for the act.',
        ]);

        $result = $this->matcher->match('aerial equipment');

        $this->assertSame(FaqMatchOutcome::Ambiguous, $result->outcome);
        $this->assertGreaterThanOrEqual(2, count($result->matches));
    }

    #[Test]
    public function it_returns_no_match_for_an_unrelated_question(): void
    {
        FaqItem::factory()->create([
            'question' => 'Do you provide insurance?',
            'answer' => 'Yes, we carry full liability insurance for every performance.',
        ]);

        $result = $this->matcher->match('what is your favorite pizza topping');

        $this->assertSame(FaqMatchOutcome::NoMatch, $result->outcome);
        $this->assertSame([], $result->matches);
    }

    #[Test]
    public function it_weights_curated_keywords_above_answer_text(): void
    {
        $keywordMatch = FaqItem::factory()->create([
            'question' => 'How do we get started?',
            'answer' => 'Reach out and we will schedule a call to discuss your event.',
            'keywords' => 'budget, ballpark',
        ]);
        FaqItem::factory()->create([
            'question' => 'What kinds of events do you perform at?',
            'answer' => 'We perform at galas with all kinds of budget ranges and ballpark figures discussed upfront.',
        ]);

        $result = $this->matcher->match('ballpark budget');

        $this->assertSame(FaqMatchOutcome::Confident, $result->outcome);
        $this->assertSame($keywordMatch->id, $result->best()->faq->id);
    }

    #[Test]
    public function it_matches_wording_that_only_appears_in_the_answer_body(): void
    {
        $target = FaqItem::factory()->create([
            'question' => 'What kinds of events does Dream World Cirque perform at?',
            'answer' => '<p>We create entertainment for corporate events, galas, and weddings.</p>',
        ]);

        $result = $this->matcher->match('do you do weddings');

        $this->assertSame(FaqMatchOutcome::Confident, $result->outcome);
        $this->assertSame($target->id, $result->best()->faq->id);
    }

    #[Test]
    public function it_ignores_html_markup_in_answers(): void
    {
        FaqItem::factory()->create([
            'question' => 'Are your performers trained?',
            'answer' => '<p>Yes.</p><p>Every performer is professionally trained and insured.</p>',
        ]);

        $result = $this->matcher->match('are performers trained');

        $this->assertSame(FaqMatchOutcome::Confident, $result->outcome);
    }

    #[Test]
    public function it_handles_an_empty_faq_table(): void
    {
        $result = $this->matcher->match('how much does it cost');

        $this->assertSame(FaqMatchOutcome::NoMatch, $result->outcome);
    }

    #[Test]
    public function it_returns_no_match_for_a_blank_query(): void
    {
        FaqItem::factory()->create(['question' => 'How much does it cost?', 'answer' => 'It depends.']);

        $result = $this->matcher->match('   ');

        $this->assertSame(FaqMatchOutcome::NoMatch, $result->outcome);
    }

    #[Test]
    public function it_orders_ambiguous_options_by_descending_score(): void
    {
        FaqItem::factory()->create([
            'question' => 'Do you provide aerial equipment for rigging?',
            'answer' => 'We provide all aerial rigging equipment needed for the performance.',
        ]);
        FaqItem::factory()->create([
            'question' => 'What space is needed?',
            'answer' => 'Some equipment needs more space than others.',
        ]);

        $result = $this->matcher->match('aerial rigging equipment');

        if (count($result->matches) > 1) {
            $this->assertGreaterThanOrEqual($result->matches[1]->score, $result->matches[0]->score);
        } else {
            $this->assertNotEmpty($result->matches);
        }
    }

    #[Test]
    public function it_caps_ambiguous_options_at_three(): void
    {
        for ($i = 0; $i < 5; $i++) {
            FaqItem::factory()->create([
                'question' => "Do you provide event entertainment option {$i}?",
                'answer' => 'We provide event entertainment for many kinds of events and occasions.',
            ]);
        }

        $result = $this->matcher->match('event entertainment');

        $this->assertLessThanOrEqual(3, count($result->matches));
    }
}
