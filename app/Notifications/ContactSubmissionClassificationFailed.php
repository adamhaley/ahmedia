<?php

namespace App\Notifications;

use App\Filament\Resources\ContactSubmissions\ContactSubmissionResource;
use App\Models\ContactSubmission;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class ContactSubmissionClassificationFailed extends Notification
{
    /**
     * Sent synchronously from the job's failed() hook, so the alert does not
     * depend on the same queue that just gave up.
     */
    public function __construct(public ContactSubmission $submission, public string $reason) {}

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
            ->error()
            ->subject('AH Media spam classification failed: '.$this->submission->name)
            ->greeting('Spam classification failed')
            ->line('The AI classification for '.$this->submission->name.' gave up after retrying.')
            ->line('Reason: '.Str::limit($this->reason, 300))
            ->line('Check storage/logs/laravel.log on the server for the underlying error (for example an expired OpenAI key).')
            ->action('Open submission', ContactSubmissionResource::getUrl('view', ['record' => $this->submission], panel: 'admin'));
    }
}
