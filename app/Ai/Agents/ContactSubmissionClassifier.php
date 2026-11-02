<?php

namespace App\Ai\Agents;

use App\Enums\ContactSubmissionClassification;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Promptable;
use Stringable;

class ContactSubmissionClassifier implements Agent, HasStructuredOutput
{
    use Promptable;

    public function instructions(): Stringable|string
    {
        return <<<'INSTRUCTIONS'
You classify inbound inquiries for AH Media (ahmedia.ai), a small studio that builds
websites, AI chatbots and automations for small businesses, including fixed-price
packages. Inquiries arrive from the site's contact form or from visitors whose question
the site's chatbot couldn't answer.

Classes:
- valid: a genuine inquiry from someone interested in AH Media's services, or a real
  question about them (pricing, scope, timelines, how something works).
- spam: bot-generated link drops, unrelated marketing/SEO/lead-gen pitches, or clearly
  automated noise.
- unanswerable: a real message, but not something this business can act on (job
  applications, vendor solicitations, personal messages unrelated to the services).
- needs_human: ambiguous, fragmented, or not safe to classify confidently.

Rules:
- Be conservative. Use needs_human when uncertain rather than guessing.
- Do not draft a reply here — only classify.
- A short, generic message ("what is your price?") with no detail about the visitor's
  business or project, written in a language unrelated to the site's English content, is
  typical of template spam campaigns that translate one message into many languages.
  Treat it as a strong spam signal, especially when the name looks like a handle rather
  than a person's name. A specific question in another language is still a real inquiry.
- For a sparse inquiry (little or no message), weigh the email address itself as a
  signal: a plausible human name in the local part, especially one that resembles the
  submitted name, suggests a real person even with nothing else to go on. A random or
  gobbledygook local part is a negative signal in the same situation. Treat this as one
  input among several, not a standalone verdict — a real inquiry can still arrive with a
  mismatched or unrelated-looking address, and a bot can register on a legitimate
  provider with a name-like local part.
- The email domain check (whether its mail servers accept mail at all) is a separate,
  weaker signal — it can rule out an obviously fake domain, but says nothing about
  whether the specific address is real, so a passing check does not on its own indicate
  a genuine inquiry.
INSTRUCTIONS;
    }

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'classification' => $schema->string()
                ->enum(array_map(fn (ContactSubmissionClassification $case): string => $case->value, ContactSubmissionClassification::cases()))
                ->required(),
            'confidence' => $schema->integer()->min(0)->max(100)->required(),
            'reason' => $schema->string()->required(),
        ];
    }
}
