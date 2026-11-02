<?php

namespace Tests\Jobs;

use App\Ai\Agents\ContactSubmissionClassifier;
use App\Enums\ContactSubmissionClassification;
use App\Jobs\ClassifyContactSubmission;
use App\Models\ContactSubmission;
use App\Notifications\ContactSubmissionClassificationFailed;
use App\Services\ContactSubmissions\ContactSubmissionClassificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Tests\TestCase;

#[CoversClass(ClassifyContactSubmission::class)]
class ClassifyContactSubmissionTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_is_queued_when_a_submission_is_created(): void
    {
        Queue::fake();

        $submission = ContactSubmission::factory()->create();

        Queue::assertPushed(ClassifyContactSubmission::class, fn (ClassifyContactSubmission $job): bool => $job->contactSubmissionId === $submission->id);
    }

    #[Test]
    public function it_classifies_the_submission(): void
    {
        ContactSubmissionClassifier::fake(fn (): array => ['classification' => 'valid', 'confidence' => 90, 'reason' => 'Asks about a website build.']);

        $submission = ContactSubmission::factory()->create();

        $this->assertSame(ContactSubmissionClassification::Valid, $submission->fresh()->classification);
    }

    #[Test]
    public function it_leaves_an_already_classified_submission_alone(): void
    {
        Queue::fake();
        $submission = ContactSubmission::factory()->create(['classification' => ContactSubmissionClassification::Valid, 'classified_at' => now()]);

        (new ClassifyContactSubmission($submission->id))->handle(app(ContactSubmissionClassificationService::class));

        ContactSubmissionClassifier::assertNeverPrompted();
    }

    #[Test]
    public function it_emails_an_ops_alert_when_it_finally_fails(): void
    {
        Notification::fake();
        Queue::fake();
        config(['mail.ops_alert_address' => 'ops@example.com']);
        $submission = ContactSubmission::factory()->create();

        (new ClassifyContactSubmission($submission->id))->failed(new RuntimeException('Your API key has expired.'));

        Notification::assertSentOnDemand(
            ContactSubmissionClassificationFailed::class,
            fn (ContactSubmissionClassificationFailed $notification, array $channels, AnonymousNotifiable $notifiable): bool => $notification->submission->is($submission)
                && $notification->reason === 'Your API key has expired.'
                && $notifiable->routes['mail'] === 'ops@example.com',
        );
    }

    #[Test]
    public function it_sends_no_alert_when_the_submission_no_longer_exists(): void
    {
        Notification::fake();

        (new ClassifyContactSubmission(999))->failed(new RuntimeException('Gone.'));

        Notification::assertNothingSent();
    }
}
