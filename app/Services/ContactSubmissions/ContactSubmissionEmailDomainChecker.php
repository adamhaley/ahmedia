<?php

namespace App\Services\ContactSubmissions;

use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\DNSCheckValidation;
use Egulias\EmailValidator\Validation\MultipleValidationWithAnd;
use Egulias\EmailValidator\Validation\RFCValidation;

class ContactSubmissionEmailDomainChecker
{
    /**
     * Whether the email's domain has mail servers willing to accept mail --
     * not whether the specific mailbox exists. A real inquiry can still have
     * a typo'd address and fail this, and a bot can still register on a
     * legitimate free provider and pass it; this is one signal among
     * several, not a verdict, so callers should feed it to a human or an AI
     * classifier alongside message content rather than acting on it alone.
     */
    public function hasMailExchanger(string $email): bool
    {
        return app(EmailValidator::class)->isValid($email, new MultipleValidationWithAnd([
            new RFCValidation,
            new DNSCheckValidation,
        ]));
    }
}
