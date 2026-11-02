<?php

namespace App\Services\ContactSubmissions;

use App\Ai\Agents\ContactSubmissionClassifier;
use App\Enums\ContactSubmissionClassification;
use App\Models\ContactSubmission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Ported from dreamworldcirque's ProspectClassificationService; the two are
 * kept aligned for extraction into a shared Prism CMS package.
 */
class ContactSubmissionClassificationService
{
    private const MaxTrainingExamples = 8;

    public function __construct(private ContactSubmissionEmailDomainChecker $emailDomainChecker) {}

    public function classify(ContactSubmission $submission): ContactSubmission
    {
        $trainingExamples = $this->reviewedExamplesFor($submission);
        $emailMxValid = $this->emailDomainChecker->hasMailExchanger($submission->email);

        $response = app(ContactSubmissionClassifier::class)->prompt($this->prompt($submission, $trainingExamples, $emailMxValid));

        $submission->update([
            'email_mx_valid' => $emailMxValid,
            'classification' => $this->classification($response['classification'] ?? null),
            'classification_confidence' => $this->confidence($response['confidence'] ?? null),
            'classification_reason' => is_string($response['reason'] ?? null)
                ? $response['reason']
                : 'Classifier did not provide a usable reason.',
            'classified_at' => now(),
        ]);

        return $submission->refresh();
    }

    /**
     * @param  Collection<int, ContactSubmission>  $trainingExamples
     */
    private function prompt(ContactSubmission $submission, Collection $trainingExamples, bool $emailMxValid): string
    {
        return sprintf(
            <<<'PROMPT'
Classify this inbound inquiry.

Use the human-reviewed examples as guidance. The human decision is correct for each
example — use them to calibrate what this business considers a valid inquiry, spam,
or unanswerable, rather than copying any single example blindly.

Human-reviewed examples:
%s

Inquiry:
Name: %s
Email: %s
Source: %s
Email domain accepts mail: %s
Message: %s

A failing email domain check means the address's domain has no mail servers willing
to accept mail -- it does not by itself mean the inquiry is fake. A genuine inquiry
can still have a typo'd email address, and a bot can register on a legitimate free
provider and pass this check. Weigh it alongside the message content, not as a
standalone verdict.
PROMPT,
            $this->formatTrainingExamples($trainingExamples),
            $submission->name,
            $submission->email,
            $submission->source->getLabel(),
            $emailMxValid ? 'yes' : 'no',
            Str::limit($submission->message, 1000),
        );
    }

    /**
     * Up to three reviewed submissions the AI got wrong, then up to two of
     * each human decision, so the prompt carries both corrections and
     * typical cases.
     *
     * @return Collection<int, ContactSubmission>
     */
    private function reviewedExamplesFor(ContactSubmission $submission): Collection
    {
        $examples = $this->reviewedExampleQuery($submission)
            ->whereNotNull('classification')
            ->whereColumn('classification', '!=', 'human_classification')
            ->latest('reviewed_at')
            ->limit(3)
            ->get();

        foreach ([ContactSubmissionClassification::Valid, ContactSubmissionClassification::Spam, ContactSubmissionClassification::Unanswerable] as $classification) {
            $examples = $examples->merge(
                $this->reviewedExampleQuery($submission)
                    ->where('human_classification', $classification->value)
                    ->latest('reviewed_at')
                    ->limit(2)
                    ->get(),
            );
        }

        return $examples->unique('id')->take(self::MaxTrainingExamples)->values();
    }

    /** @return Builder<ContactSubmission> */
    private function reviewedExampleQuery(ContactSubmission $submission): Builder
    {
        return ContactSubmission::query()
            ->select(['id', 'name', 'email', 'message', 'classification', 'human_classification', 'reviewed_at'])
            ->whereKeyNot($submission->id)
            ->whereNotNull('human_classification')
            ->whereNotNull('reviewed_at');
    }

    /**
     * @param  Collection<int, ContactSubmission>  $trainingExamples
     */
    private function formatTrainingExamples(Collection $trainingExamples): string
    {
        if ($trainingExamples->isEmpty()) {
            return 'No human-reviewed examples are available yet.';
        }

        return $trainingExamples
            ->map(fn (ContactSubmission $example, int $index): string => sprintf(
                <<<'EXAMPLE'
                Example %d:
                Name: %s
                Email: %s
                Message: %s
                Original AI classification: %s
                Correct human decision: %s
                EXAMPLE,
                $index + 1,
                $example->name,
                $example->email,
                Str::limit($example->message, 300),
                $example->classification?->getLabel() ?? 'Unclassified',
                $example->human_classification?->getLabel(),
            ))
            ->implode("\n\n");
    }

    private function classification(mixed $classification): ContactSubmissionClassification
    {
        return is_string($classification)
            ? (ContactSubmissionClassification::tryFrom($classification) ?? ContactSubmissionClassification::NeedsHuman)
            : ContactSubmissionClassification::NeedsHuman;
    }

    private function confidence(mixed $confidence): int
    {
        return is_numeric($confidence) ? max(0, min(100, (int) $confidence)) : 0;
    }
}
