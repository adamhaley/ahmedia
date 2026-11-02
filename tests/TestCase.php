<?php

namespace Tests;

use App\Ai\Agents\ContactSubmissionClassifier;
use App\Services\ContactSubmissions\ContactSubmissionEmailDomainChecker;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Every ContactSubmission queues a classification, which runs inline on
     * the test suite's sync queue. Default it to a canned answer so no test
     * reaches OpenAI or the network; tests that care fake it themselves.
     */
    protected function setUp(): void
    {
        parent::setUp();

        ContactSubmissionClassifier::fake(fn (): array => ['classification' => 'needs_human', 'confidence' => 0, 'reason' => 'Faked in tests.']);
        $this->mock(ContactSubmissionEmailDomainChecker::class)->shouldReceive('hasMailExchanger')->andReturn(true)->byDefault();
    }
}
