<?php

namespace Database\Seeders;

use App\Enums\PageTemplate;
use App\Models\FaqItem;
use App\Models\Page;
use Illuminate\Database\Seeder;

/**
 * Seeds the FAQs (shared by the /faq/ page and the chatbot) and the /faq/
 * page itself. Like the other content seeders, it only runs against an empty
 * table so CMS edits are never overwritten; the page is created with
 * firstOrCreate because AhmediaContentSeeder skips once any page exists.
 */
class FaqContentSeeder extends Seeder
{
    public function run(): void
    {
        if (FaqItem::query()->exists()) {
            $this->command?->warn('FAQs already exist; skipping so CMS edits are never overwritten.');

            return;
        }

        foreach (require __DIR__.'/data/faqs.php' as $sortOrder => $faq) {
            FaqItem::query()->create([...$faq, 'sort_order' => $sortOrder]);
        }

        Page::query()->firstOrCreate(
            ['parent_id' => null, 'slug' => 'faq'],
            [
                'title' => 'FAQ',
                'template' => PageTemplate::Faq,
                'is_published' => true,
                'show_in_nav' => false,
                'section_eyebrow' => 'FAQ',
                'hero_heading' => 'Questions, answered',
                'hero_subheading' => "Services, pricing and how projects work. Can't find yours? Ask in the chat or send a message.",
                'cta_eyebrow' => 'Ready When You Are',
                'cta_heading' => "Let's talk about your project",
                'cta_button_label' => 'Start a Project',
            ],
        );
    }
}
