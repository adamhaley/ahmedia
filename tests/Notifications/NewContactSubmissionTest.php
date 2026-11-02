<?php

namespace Tests\Notifications;

use App\Enums\ContactSubmissionClassification;
use App\Models\ContactSubmission;
use App\Notifications\NewContactSubmission;
use Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(NewContactSubmission::class)]
class NewContactSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public static function classificationProvider(): Generator
    {
        yield 'classification pending' => [null, null, true];
        yield 'spam below threshold' => [ContactSubmissionClassification::Spam, 94, true];
        yield 'high-confidence spam' => [ContactSubmissionClassification::Spam, 95, false];
        yield 'high-confidence valid lead' => [ContactSubmissionClassification::Valid, 99, true];
    }

    #[Test]
    #[DataProvider('classificationProvider')]
    public function it_sends_unless_the_submission_is_high_confidence_spam(?ContactSubmissionClassification $classification, ?int $confidence, bool $expectedToSend): void
    {
        Queue::fake();
        $submission = ContactSubmission::factory()->create();
        $notification = new NewContactSubmission($submission);
        ContactSubmission::query()->whereKey($submission->id)->update([
            'classification' => $classification?->value,
            'classification_confidence' => $confidence,
        ]);

        $this->assertSame($expectedToSend, $notification->shouldSend(new AnonymousNotifiable, 'mail'));
    }
}
