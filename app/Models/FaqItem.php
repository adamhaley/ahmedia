<?php

namespace App\Models;

use Database\Factories\FaqItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['question', 'answer', 'keywords', 'sort_order'])]
class FaqItem extends Model
{
    /** @use HasFactory<FaqItemFactory> */
    use HasFactory;
}
