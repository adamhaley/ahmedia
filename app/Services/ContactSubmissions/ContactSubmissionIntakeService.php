<?php

declare(strict_types=1);

namespace App\Services\ContactSubmissions;

use App\Enums\ContactSubmissionSource;
use App\Models\ContactSubmission;
use App\Notifications\NewContactSubmission;
use Illuminate\Support\Facades\Notification;
use Throwable;

/**
 * The single path every live intake channel creates a ContactSubmission
 * through -- the contact form and the chatbot fallback both call this, the
 * same shape as dreamworldcirque's ProspectIntakeService.
 */
class ContactSubmissionIntakeService
{
    public function record(
        ContactSubmissionSource $source,
        string $name,
        string $email,
        string $message,
        ?string $submittedFrom,
        ?string $ipAddress,
    ): ContactSubmission {
        $submission = ContactSubmission::query()->create([
            // Set explicitly rather than left to the column's DB-level
            // default: create() doesn't refetch the row, so the in-memory
            // instance would otherwise have a null source.
            'source' => $source,
            'name' => $name,
            'email' => $email,
            'message' => $message,
            'submitted_from' => $submittedFrom,
            'ip_address' => $ipAddress,
        ]);

        // Delayed so the queued classification usually lands first, letting
        // NewContactSubmission::shouldSend() hold back high-confidence spam.
        // The lead is already saved, so a failure to queue it is reported but
        // never shown to the visitor or allowed to lose it.
        try {
            Notification::route('mail', config('mail.lead_notification_address'))
                ->notify((new NewContactSubmission($submission))->delay(now()->addMinutes(2))->afterCommit());
        } catch (Throwable $exception) {
            report($exception);
        }

        return $submission;
    }
}
