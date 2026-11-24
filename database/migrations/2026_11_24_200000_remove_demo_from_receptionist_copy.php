<?php

use App\Models\FaqItem;
use App\Models\Page;
use Illuminate\Database\Migrations\Migration;

/**
 * Replaces "demo receptionist" (any case) with "24/7 AI Receptionist" in all visible page copy and
 * FAQ answers. FAQ keywords are left alone: "demo" there is chatbot matching phrasing, not copy.
 * Only rows whose values actually change are saved.
 */
return new class extends Migration
{
    private const PATTERN = '/\bdemo receptionist\b/i';

    private const REPLACEMENT = '24/7 AI Receptionist';

    /** @var list<string> */
    private const PAGE_TEXT_COLUMNS = [
        'title', 'meta_title', 'meta_description', 'hero_heading', 'hero_subheading', 'section_eyebrow',
        'section_heading', 'body', 'card_excerpt', 'cta_eyebrow', 'cta_heading', 'cta_copy', 'cta_button_label',
        'circle_image_alt',
    ];

    /** @var list<string> */
    private const PAGE_LIST_COLUMNS = ['hero_points', 'bullets', 'approach_steps'];

    public function up(): void
    {
        foreach (Page::query()->get() as $page) {
            foreach (self::PAGE_TEXT_COLUMNS as $column) {
                $page->{$column} = $this->reword($page->{$column});
            }

            foreach (self::PAGE_LIST_COLUMNS as $column) {
                $page->{$column} = $this->rewordAll($page->{$column});
            }

            $page->side_card = $this->rewordAll($page->side_card, ['link_url']);

            $buttons = $page->buttons;
            if (is_array($buttons)) {
                $page->buttons = array_map(
                    fn ($button) => is_array($button) ? $this->rewordAll($button, ['url', 'style']) : $button,
                    $buttons,
                );
            }

            if ($page->isDirty()) {
                $page->save();
            }
        }

        foreach (FaqItem::query()->get() as $faq) {
            $faq->answer = $this->reword($faq->answer);

            if ($faq->isDirty()) {
                $faq->save();
            }
        }
    }

    public function down(): void
    {
        // Data migration: the previous wording is not restored.
    }

    private function reword(mixed $value): mixed
    {
        return is_string($value) ? preg_replace(self::PATTERN, self::REPLACEMENT, $value) : $value;
    }

    /**
     * @param  list<string>  $skipKeys
     */
    private function rewordAll(mixed $values, array $skipKeys = []): mixed
    {
        if (! is_array($values)) {
            return $values;
        }

        foreach ($values as $key => $value) {
            if (! in_array($key, $skipKeys, true)) {
                $values[$key] = $this->reword($value);
            }
        }

        return $values;
    }
};
