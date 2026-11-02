<?php

namespace Tests\Models;

use App\Enums\ContactSubmissionClassification;
use App\Models\ContactSubmission;
use Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(ContactSubmission::class)]
class ContactSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public static function spamProvider(): Generator
    {
        yield 'unclassified' => [null, null, true];
        yield 'AI says valid' => [ContactSubmissionClassification::Valid, null, true];
        yield 'AI says spam' => [ContactSubmissionClassification::Spam, null, false];
        yield 'human says spam' => [ContactSubmissionClassification::Valid, ContactSubmissionClassification::Spam, false];
        yield 'AI says spam, human overrules' => [ContactSubmissionClassification::Spam, ContactSubmissionClassification::Valid, true];
    }

    #[Test]
    #[DataProvider('spamProvider')]
    public function it_excludes_spam_preferring_the_human_decision(?ContactSubmissionClassification $classification, ?ContactSubmissionClassification $humanClassification, bool $expectedVisible): void
    {
        Queue::fake();
        ContactSubmission::factory()->create(['classification' => $classification, 'human_classification' => $humanClassification]);

        $this->assertSame($expectedVisible, ContactSubmission::query()->notSpam()->exists());
    }
}
