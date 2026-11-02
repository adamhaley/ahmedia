<?php

namespace App\Jobs;

use App\Models\ContactSubmission;
use App\Notifications\ContactSubmissionClassificationFailed;
use App\Services\ContactSubmissions\ContactSubmissionClassificationService;
use DateTimeInterface;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Support\Facades\Notification;
use Throwable;

class ClassifyContactSubmission implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public int $tries = 0;

    public int $timeout = 120;

    public int $uniqueFor = 600;

    public function __construct(public int $contactSubmissionId) {}

    /** @return array<int, int> */
    public function backoff(): array
    {
        return [5, 30, 120];
    }

    /** @return array<int, object> */
    public function middleware(): array
    {
        return [(new RateLimited('openai'))->releaseAfter(30)];
    }

    public function retryUntil(): DateTimeInterface
    {
        return now()->addMinutes(30);
    }

    public function uniqueId(): string
    {
        return (string) $this->contactSubmissionId;
    }

    public function handle(ContactSubmissionClassificationService $classifier): void
    {
        $submission = ContactSubmission::query()->find($this->contactSubmissionId);

        if ($submission === null || $submission->classified_at !== null) {
            return;
        }

        $classifier->classify($submission);
    }

    public function failed(?Throwable $exception): void
    {
        $submission = ContactSubmission::query()->find($this->contactSubmissionId);

        if ($submission !== null) {
            Notification::route('mail', config('mail.ops_alert_address'))
                ->notify(new ContactSubmissionClassificationFailed($submission, $exception?->getMessage() ?? 'Unknown error'));
        }
    }
}
