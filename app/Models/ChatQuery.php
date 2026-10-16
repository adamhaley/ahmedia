<?php

namespace App\Models;

use App\Enums\FaqMatchOutcome;
use Database\Factories\ChatQueryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['question', 'outcome', 'score', 'matched_faq_id', 'contact_submission_id'])]
class ChatQuery extends Model
{
    /** @use HasFactory<ChatQueryFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'outcome' => FaqMatchOutcome::class,
            'score' => 'float',
        ];
    }

    /** @return BelongsTo<FaqItem, $this> */
    public function matchedFaq(): BelongsTo
    {
        return $this->belongsTo(FaqItem::class, 'matched_faq_id');
    }

    /** @return BelongsTo<ContactSubmission, $this> */
    public function contactSubmission(): BelongsTo
    {
        return $this->belongsTo(ContactSubmission::class);
    }
}
