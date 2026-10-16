<?php

namespace App\Notifications;

use App\Enums\ContactSubmissionSource;
use App\Filament\Resources\ContactSubmissions\ContactSubmissionResource;
use App\Models\ContactSubmission;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewContactSubmission extends Notification
{
    public function __construct(public ContactSubmission $submission) {}

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
