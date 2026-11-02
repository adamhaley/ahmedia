<?php

namespace App\Models;

use App\Enums\ContactSubmissionClassification;
use App\Enums\ContactSubmissionSource;
use App\Jobs\ClassifyContactSubmission;
use Database\Factories\ContactSubmissionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'name', 'email', 'email_mx_valid', 'message', 'source', 'submitted_from', 'ip_address', 'read_at',
    'classification', 'classification_confidence', 'classification_reason', 'classified_at',
    'human_classification', 'reviewed_by_user_id', 'reviewed_at',
])]
class ContactSubmission extends Model
{
    /** @use HasFactory<ContactSubmissionFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'source' => ContactSubmissionSource::class,
            'read_at' => 'datetime',
            'email_mx_valid' => 'boolean',
            'classification' => ContactSubmissionClassification::class,
            'classification_confidence' => 'integer',
            'classified_at' => 'datetime',
            'human_classification' => ContactSubmissionClassification::class,
            'reviewed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::created(function (ContactSubmission $submission): void {
            ClassifyContactSubmission::dispatch($submission->id);
        });
    }

    /** @return BelongsTo<User, $this> */
    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }

    /**
     * Excludes spam: a reviewer's decision when there is one, otherwise the
     * AI's. Unlike dreamworldcirque's Prospect::notSpam(), a human marking an
     * AI-flagged submission as valid brings it back.
     */
    #[Scope]
    protected function notSpam(Builder $query): void
    {
        $query->where(function (Builder $query): void {
            $query
                ->where('human_classification', '!=', ContactSubmissionClassification::Spam->value)
                ->orWhere(function (Builder $query): void {
                    $query
                        ->whereNull('human_classification')
                        ->where(function (Builder $query): void {
                            $query
                                ->whereNull('classification')
                                ->orWhere('classification', '!=', ContactSubmissionClassification::Spam->value);
                        });
                });
        });
    }

    /**
     * Records a reviewer's call. Reviewed submissions become the examples the
     * classifier calibrates against (see ContactSubmissionClassificationService).
     */
    public function markClassification(ContactSubmissionClassification $classification, ?int $reviewerId = null): bool
    {
        return $this->update([
            'human_classification' => $classification,
            'reviewed_by_user_id' => $reviewerId,
            'reviewed_at' => now(),
        ]);
    }
}
