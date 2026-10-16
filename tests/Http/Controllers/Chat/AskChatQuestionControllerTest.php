<?php

namespace Tests\Http\Controllers\Chat;

use App\Enums\FaqMatchOutcome;
use App\Http\Controllers\Chat\AskChatQuestionController;
use App\Models\ChatQuery;
use App\Models\FaqItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(AskChatQuestionController::class)]
class AskChatQuestionControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    #[DataProvider('smallTalk')]
    public function it_replies_to_small_talk_without_logging_a_faq_attempt(string $question, string $outcome): void
    {
        $this->postJson('/chat/ask', ['question' => $question])
            ->assertOk()->assertJsonPath('outcome', $outcome)
            ->assertJsonPath('matches', [])->assertJsonStructure(['reply']);

        $this->assertDatabaseCount('chat_queries', 0);
    }

    public static function smallTalk(): array
    {
        return [['Hello!', 'greeting'], ['Thank you!', 'farewell']];
    }

    #[Test]
    public function it_matches_a_typo_in_a_question_with_a_greeting_and_logs_the_original_input(): void
    {
        $faq = FaqItem::factory()->create([
            'question' => 'Do you provide aerial performances?',
            'answer' => '<p>Yes, we provide aerial performances.</p>',
            'keywords' => 'aerial',
        ]);

        $this->postJson('/chat/ask', ['question' => 'Hi, aerail?'])
            ->assertOk()->assertJsonPath('outcome', 'confident')
            ->assertJsonPath('matches.0.id', $faq->id);

        $this->assertDatabaseHas('chat_queries', [
            'question' => 'Hi, aerail?',
            'matched_faq_id' => $faq->id,
        ]);
    }

    #[Test]
    public function it_returns_the_matched_faq_answer(): void
    {
        FaqItem::factory()->create([
            'question' => 'Do you provide insurance?',
            'answer' => '<p>Yes, full liability insurance for every performance.</p>',
            'keywords' => 'insured, liability, coverage',
        ]);

        $response = $this->postJson('/chat/ask', ['question' => 'are you insured']);

        $response->assertOk()->assertJson([
            'outcome' => 'confident',
        ]);
        $this->assertSame('Do you provide insurance?', $response->json('matches.0.question'));
    }

    #[Test]
    public function it_answers_directly_when_only_one_faq_qualifies(): void
    {
        foreach (require database_path('seeders/data/faqs.php') as $index => $item) {
            FaqItem::factory()->create([...$item, 'sort_order' => $index]);
        }

        $faq = FaqItem::query()
            ->where('question', 'My website has been hacked. Can you help?')
            ->firstOrFail();

        $this->postJson('/chat/ask', ['question' => 'my wordpress site got hacked'])
            ->assertOk()
            ->assertJsonPath('outcome', 'confident')
            ->assertJsonCount(1, 'matches')
            ->assertJsonPath('matches.0.id', $faq->id)
            ->assertJsonPath('matches.0.answer', $faq->answer);

        $this->assertDatabaseHas('chat_queries', [
            'question' => 'my wordpress site got hacked',
            'outcome' => FaqMatchOutcome::Confident->value,
            'matched_faq_id' => $faq->id,
        ]);
    }

    #[Test]
    public function it_returns_ambiguous_options_when_scores_are_close(): void
    {
        FaqItem::factory()->create([
            'question' => 'Do you provide aerial rigging equipment?',
            'answer' => 'We bring our own aerial rigging equipment to every show.',
        ]);
        FaqItem::factory()->create([
            'question' => 'How much space do you need for aerial equipment?',
            'answer' => 'Aerial equipment needs enough ceiling height for the act.',
        ]);

        $response = $this->postJson('/chat/ask', ['question' => 'aerial equipment']);

        $response->assertOk()->assertJson(['outcome' => 'ambiguous']);
        $this->assertGreaterThanOrEqual(2, count($response->json('matches')));
    }

    #[Test]
    public function it_returns_no_matches_when_nothing_scores(): void
    {
        FaqItem::factory()->create(['question' => 'Do you provide insurance?', 'answer' => 'Yes.']);

        $response = $this->postJson('/chat/ask', ['question' => 'what is your favorite pizza topping']);

        $response->assertOk()->assertJson(['outcome' => 'no_match', 'matches' => []]);
    }

    #[Test]
    public function it_rejects_a_blank_question(): void
    {
        $this->postJson('/chat/ask', ['question' => ''])->assertUnprocessable();
    }

    #[Test]
    public function it_rejects_an_overlong_question(): void
    {
        $this->postJson('/chat/ask', ['question' => str_repeat('a', 501)])->assertUnprocessable();
    }

    #[Test]
    public function it_logs_the_query_with_its_outcome_and_matched_faq(): void
    {
        $faq = FaqItem::factory()->create([
            'question' => 'Do you provide insurance?',
            'answer' => 'Yes, full liability insurance.',
            'keywords' => 'insured, liability',
        ]);

        $this->postJson('/chat/ask', ['question' => 'are you insured']);

        $this->assertDatabaseHas('chat_queries', [
            'question' => 'are you insured',
            'outcome' => FaqMatchOutcome::Confident->value,
            'matched_faq_id' => $faq->id,
        ]);
    }

    #[Test]
    public function it_logs_a_no_match_query_without_a_faq_id(): void
    {
        FaqItem::factory()->create(['question' => 'Do you provide insurance?', 'answer' => 'Yes.']);

        $this->postJson('/chat/ask', ['question' => 'what is your favorite pizza topping']);

        $log = ChatQuery::query()->latest()->first();
        $this->assertSame(FaqMatchOutcome::NoMatch, $log->outcome);
        $this->assertNull($log->matched_faq_id);
        $this->assertNull($log->score);
    }

    #[Test]
    public function it_throttles_excessive_requests(): void
    {
        FaqItem::factory()->create(['question' => 'Do you provide insurance?', 'answer' => 'Yes.']);

        for ($i = 0; $i < 20; $i++) {
            $this->postJson('/chat/ask', ['question' => 'are you insured'])->assertOk();
        }

        $this->postJson('/chat/ask', ['question' => 'are you insured'])->assertStatus(429);
    }
}
