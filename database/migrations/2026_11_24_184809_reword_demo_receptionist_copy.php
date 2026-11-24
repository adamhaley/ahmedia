<?php

use App\Models\FaqItem;
use App\Models\Page;
use Illuminate\Database\Migrations\Migration;

/**
 * Rewords the "demo receptionist" copy to talk about our AI receptionist instead. Exact-string
 * replacements, so passages already edited in the admin are left alone, and missing rows are skipped.
 */
return new class extends Migration
{
    public function up(): void
    {
        $home = Page::query()->where('path', '')->first();
        if ($home && is_array($home->hero_points)) {
            $home->update(['hero_points' => str_replace(
                'Demo receptionist: [(765) 307-5537](tel:+17653075537)',
                'Call our AI receptionist: [(765) 307-5537](tel:+17653075537)',
                $home->hero_points,
            )]);
        }

        $voice = Page::query()->where('path', 'services/voice-ai-receptionists')->first();
        if ($voice) {
            $attributes = [
                'hero_subheading' => str_replace(
                    'Call the demo: (765) 307-5537.',
                    'Call our AI receptionist: (765) 307-5537.',
                    (string) $voice->hero_subheading,
                ),
                'body' => str_replace(
                    'Call our demo receptionist at **[(765) 307-5537](tel:+17653075537)**. She answers for a sample med spa and can book you a consultation. Yours would be trained on your own business.',
                    'Call our AI receptionist at **[(765) 307-5537](tel:+17653075537)**. Yours would be trained on your own business.',
                    (string) $voice->body,
                ),
            ];

            $sideCard = $voice->side_card;
            if (is_array($sideCard) && is_string($sideCard['note'] ?? null)) {
                $sideCard['note'] = str_ireplace('demo receptionist', 'AI receptionist', $sideCard['note']);
                $attributes['side_card'] = $sideCard;
            }

            $voice->update($attributes);
        }

        $faq = FaqItem::query()->where('question', 'What is an AI voice receptionist?')->first();
        $faq?->update([
            'answer' => str_replace('call our demo receptionist at', 'call our AI receptionist at', (string) $faq->answer),
        ]);
    }

    public function down(): void
    {
        // Data migration: the previous wording is not restored.
    }
};
