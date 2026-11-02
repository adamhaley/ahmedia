<?php

namespace App\Notifications;

use App\Enums\ContactSubmissionClassification;
use App\Enums\ContactSubmissionSource;
use App\Filament\Resources\ContactSubmissions\ContactSubmissionResource;
use App\Models\ContactSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewContactSubmission extends Notification implements ShouldQueue
{
    use Queueable;

    private const HighConfidenceSpamThreshold = 95;

    public function __construct(public ContactSubmission $submission) {}

    /**
     * Checked when the delayed notification finally sends, by which time the
     * classifier has usually run: high-confidence spam stays in the admin
     * but never reaches the inbox.
     */
    public function shouldSend(object $notifiable, string $channel): bool
    {
        $submission = $this->submission->fresh();

        return $submission !== null
            && ! ($submission->classification === ContactSubmissionClassification::Spam
                && ($submission->classification_confidence ?? 0) >= self::HighConfidenceSpamThreshold);
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(($this->submission->source === ContactSubmissionSource::Chat ? 'New AH Media chat question from ' : 'New AH Media enquiry from ').$this->submission->name)
            ->replyTo($this->submission->email, $this->submission->name)
            ->greeting('New contact form submission')
            ->line('Name: '.$this->submission->name)
            ->line('Email: '.$this->submission->email)
            ->line('Message:')
            ->line($this->submission->message)
            ->action('View in admin', ContactSubmissionResource::getUrl('view', ['record' => $this->submission], panel: 'admin'));
    }
}
