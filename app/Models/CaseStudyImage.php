<?php

namespace App\Models;

use Database\Factories\CaseStudyImageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['case_study_id', 'image', 'alt_text', 'sort_order'])]
class CaseStudyImage extends Model
{
    /** @use HasFactory<CaseStudyImageFactory> */
    use HasFactory;

    /** @return BelongsTo<CaseStudy, $this> */
    public function caseStudy(): BelongsTo
    {
        return $this->belongsTo(CaseStudy::class);
    }
}
