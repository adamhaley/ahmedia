<?php

namespace Tests\Http\Controllers\Chat;

use App\Enums\ContactSubmissionSource;
use App\Http\Controllers\Chat\StoreChatLeadController;
use App\Models\ChatQuery;
use App\Models\ContactSubmission;
use App\Notifications\NewContactSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(StoreChatLeadController::class)]
class StoreChatLeadControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string, string> */
    private function lead(): array
    {
        return ['name' => 'Ada Lovelace', 'email' => 'ada@example.com', 'question' => 'Do you build Shopify apps?'];
    }

    #[Test]
    public function it_files_the_unanswered_question_in_the_contact_inbox(): void
    {
        Notification::fake();

        $this->postJson('/chat/lead', $this->lead())->assertCreated();

        $this->assertDatabaseHas('contact_submissions', [
            'email' => 'ada@example.com',
            'message' => 'Do you build Shopify apps?',
            'source' => ContactSubmissionSource::Chat->value,
        ]);
    }

    #[Test]
    public function it_emails_the_lead(): void
    {
        Notification::fake();

        $this->postJson('/chat/lead', $this->lead());

        Notification::assertSentOnDemand(NewContactSubmission::class);
    }

    #[Test]
    public function it_links_the_logged_chat_query_to_the_lead(): void
    {
        Notification::fake();
        $query = ChatQuery::factory()->create(['question' => 'Do you build Shopify apps?']);

        $this->postJson('/chat/lead', $this->lead());

        $this->assertSame(ContactSubmission::query()->value('id'), $query->fresh()->contact_submission_id);
    }

    #[Test]
    public function it_rejects_a_filled_honeypot(): void
    {
        Notification::fake();

        $this->postJson('/chat/lead', [...$this->lead(), 'website' => 'https://spam.example'])->assertUnprocessable();

        $this->assertSame(0, ContactSubmission::query()->count());
    }
}
