<?php

namespace Tests\Http\Controllers;

use App\Http\Controllers\StoreContactSubmissionController;
use App\Models\ContactSubmission;
use App\Notifications\NewContactSubmission;
use Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Tests\TestCase;

#[CoversClass(StoreContactSubmissionController::class)]
class StoreContactSubmissionControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string, string> */
    private function validSubmission(): array
    {
        return ['name' => 'Ada Lovelace', 'email' => 'ada@example.com', 'message' => 'I need a go-live review.'];
    }

    #[Test]
    public function it_stores_the_submission(): void
    {
        Notification::fake();

        $this->from('/')->post('/contact', $this->validSubmission());

        $this->assertDatabaseHas('contact_submissions', ['name' => 'Ada Lovelace', 'email' => 'ada@example.com']);
    }

    #[Test]
    public function it_emails_the_lead_notification_address(): void
    {
        Notification::fake();
        config(['mail.lead_notification_address' => 'leads@example.com']);

        $this->from('/')->post('/contact', $this->validSubmission());

        Notification::assertSentOnDemand(
            NewContactSubmission::class,
            fn (NewContactSubmission $notification, array $channels, AnonymousNotifiable $notifiable): bool => $notifiable->routes['mail'] === 'leads@example.com',
        );
    }

    #[Test]
    public function it_returns_to_the_form_with_a_thank_you(): void
    {
        Notification::fake();

        $response = $this->from('/contact/')->post('/contact', $this->validSubmission());

        $response->assertRedirect(url('/contact/').'#contact')->assertSessionHas('contact_status');
    }

    #[Test]
    public function it_keeps_the_submission_when_the_email_fails(): void
    {
        Notification::shouldReceive('route')->andThrow(new RuntimeException('SMTP down'));

        $response = $this->from('/')->post('/contact', $this->validSubmission());

        $response->assertSessionHas('contact_status');
        $this->assertSame(1, ContactSubmission::query()->count());
    }

    #[Test]
    public function it_rejects_a_filled_honeypot(): void
    {
        Notification::fake();

        $this->from('/')->post('/contact', [...$this->validSubmission(), 'website' => 'https://spam.example']);

        $this->assertSame(0, ContactSubmission::query()->count());
    }

    public static function invalidSubmissionProvider(): Generator
    {
        yield 'missing name' => ['name', ''];
        yield 'invalid email' => ['email', 'not-an-email'];
        yield 'missing message' => ['message', ''];
    }

    #[Test]
    #[DataProvider('invalidSubmissionProvider')]
    public function it_validates_the_form(string $field, string $value): void
    {
        Notification::fake();

        $response = $this->from('/')->post('/contact', [...$this->validSubmission(), $field => $value]);

        $response->assertSessionHasErrors($field);
    }

    #[Test]
    public function it_rate_limits_repeated_submissions(): void
    {
        Notification::fake();

        foreach (range(1, 5) as $attempt) {
            $this->from('/')->post('/contact', $this->validSubmission());
        }
        $response = $this->from('/')->post('/contact', $this->validSubmission());

        $response->assertTooManyRequests();
    }
}
