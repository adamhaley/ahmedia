<?php

namespace Tests\Services\ContactSubmissions;

use App\Ai\Agents\ContactSubmissionClassifier;
use App\Enums\ContactSubmissionClassification;
use App\Models\ContactSubmission;
use App\Services\ContactSubmissions\ContactSubmissionClassificationService;
use App\Services\ContactSubmissions\ContactSubmissionEmailDomainChecker;
use Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(ContactSubmissionClassificationService::class)]
class ContactSubmissionClassificationServiceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_stores_the_classifiers_verdict(): void
    {
        Queue::fake();
        ContactSubmissionClassifier::fake(fn (): array => ['classification' => 'spam', 'confidence' => 97, 'reason' => 'Template price question in Zulu.']);
        $submission = ContactSubmission::factory()->create();

        app(ContactSubmissionClassificationService::class)->classify($submission);

        $this->assertDatabaseHas('contact_submissions', [
            'id' => $submission->id,
            'classification' => 'spam',
            'classification_confidence' => 97,
            'classification_reason' => 'Template price question in Zulu.',
        ]);
    }

    #[Test]
    public function it_includes_the_email_and_domain_check_result_in_the_prompt(): void
    {
        Queue::fake();
        $this->mock(ContactSubmissionEmailDomainChecker::class)
            ->shouldReceive('hasMailExchanger')
            ->with('robertpal@example.com')
            ->andReturn(false);
        $submission = ContactSubmission::factory()->create(['email' => 'robertpal@example.com']);

        app(ContactSubmissionClassificationService::class)->classify($submission);

        ContactSubmissionClassifier::assertPrompted(fn ($prompt): bool => str_contains($prompt->prompt, 'Email: robertpal@example.com')
            && str_contains($prompt->prompt, 'Email domain accepts mail: no'));
    }

    #[Test]
    public function it_includes_human_reviewed_submissions_as_examples(): void
    {
        Queue::fake();
        ContactSubmission::factory()->create([
            'name' => 'RobertPal',
            'classification' => ContactSubmissionClassification::NeedsHuman,
            'human_classification' => ContactSubmissionClassification::Spam,
            'reviewed_at' => now(),
        ]);
        $submission = ContactSubmission::factory()->create();

        app(ContactSubmissionClassificationService::class)->classify($submission);

        ContactSubmissionClassifier::assertPrompted(fn ($prompt): bool => str_contains($prompt->prompt, 'Name: RobertPal')
            && str_contains($prompt->prompt, 'Correct human decision: Spam'));
    }

    public static function unusableResponseProvider(): Generator
    {
        yield 'unknown classification' => [['classification' => 'phishing', 'confidence' => 80, 'reason' => 'x'], ContactSubmissionClassification::NeedsHuman, 80];
        yield 'confidence above 100' => [['classification' => 'valid', 'confidence' => 140, 'reason' => 'x'], ContactSubmissionClassification::Valid, 100];
        yield 'non-numeric confidence' => [['classification' => 'spam', 'confidence' => 'high', 'reason' => 'x'], ContactSubmissionClassification::Spam, 0];
    }

    /** @param array<string, mixed> $response */
    #[Test]
    #[DataProvider('unusableResponseProvider')]
    public function it_normalises_an_unusable_response(array $response, ContactSubmissionClassification $classification, int $confidence): void
    {
        Queue::fake();
        ContactSubmissionClassifier::fake(fn (): array => $response);
        $submission = ContactSubmission::factory()->create();

        $classified = app(ContactSubmissionClassificationService::class)->classify($submission);

        $this->assertSame([$classification, $confidence], [$classified->classification, $classified->classification_confidence]);
    }
}
