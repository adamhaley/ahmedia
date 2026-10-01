<?php

namespace Database\Seeders;

use App\Enums\PageTemplate;
use App\Models\Page;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

/**
 * Seeds ahmedia.ai's current content, ported from Codette's site.json
 * (codette/sites/ahmedia/site.json) plus the 4-service split and real
 * package/pricing copy decided during this port (see
 * wiki/pages/ahmedia-packages-offerings.md for the source of truth on
 * pricing -- this seeder transcribes it, it is not the canonical record).
 */
class AhmediaContentSeeder extends Seeder
{
    public function run(): void
    {
        SiteSetting::current()->update([
            'contact_email' => null,
            'phone' => null,
            'accent_color' => null,
        ]);

        $home = Page::query()->updateOrCreate(
            ['path' => ''],
            [
                'title' => 'AH Media',
                'slug' => null,
                'template' => PageTemplate::Home,
                'is_published' => true,
                'show_in_nav' => false,
                'section_eyebrow' => 'Approach',
                'section_heading' => "Static where it's simple, AI where it earns its keep",
                'body' => 'Most of what a business needs from a website is speed and clarity. The AI does the actual work behind it — qualifying leads, answering questions, and automating the busywork between the tools you already use.',
                'bullets' => [
                    'n8n as the automation and integration layer between your existing tools',
                    'Custom chat and RAG assistants trained on your own content',
                    'Static-first frontends that stay fast and cheap to run',
                ],
                'cta_eyebrow' => 'Case Studies',
                'cta_heading' => 'Real work, coming soon',
            ]
        );

        $servicesIndex = Page::query()->updateOrCreate(
            ['parent_id' => null, 'slug' => 'services'],
            [
                'title' => 'Services',
                'template' => PageTemplate::ServicesIndex,
                'is_published' => true,
                'show_in_nav' => true,
                'nav_anchor' => 'services',
                'section_eyebrow' => 'Services',
                'section_heading' => 'AI Consulting, Custom Programming, Website Design, Voice AI Receptionists',
                'body' => 'Each engagement starts with a real bottleneck, not a technology mandate.',
                'hero_heading' => 'What We Do',
                'hero_subheading' => 'Four ways we help businesses run on AI — pick a starting point, or talk through where you actually are.',
                'cta_eyebrow' => 'Ready When You Are',
                'cta_heading' => 'Let\'s talk about your project',
                'cta_button_label' => 'Start a Project',
            ]
        );

        $services = [
            [
                'slug' => 'ai-consulting',
                'title' => 'AI Consulting',
                'card_excerpt' => "Strategy, opportunity mapping, and a clear path to production — not just a slide deck. We help you decide what's worth building and what isn't.",
                'hero_subheading' => 'Strategy, opportunity mapping, and a clear path to production — not just a slide deck.',
                'section_heading' => 'Pricing',
                'body' => 'À la carte or on retainer, depending on how much ongoing work you need.',
                'bullets' => [
                    '$95/hr à la carte',
                    'Starter retainer: $1000/mo for up to 10 hrs of custom automation/consulting work',
                    'Growth retainer: $2000/mo for up to 25 hrs of custom automation/consulting work',
                ],
            ],
            [
                'slug' => 'custom-programming',
                'title' => 'Custom Programming',
                'card_excerpt' => 'Web apps, APIs, and automation tooling, including n8n-driven workflows that connect the systems you already run on.',
                'hero_subheading' => 'Web apps, APIs, and automation tooling, including n8n-driven workflows that connect the systems you already run on.',
                'section_heading' => 'Pricing',
                // Shares AI Consulting's pricing structure exactly, decided 2026-10-01 --
                // left generic/unscoped for now per Adam ("we can leave it generic for
                // now, $95/hr is fine"), not a placeholder.
                'body' => 'Same structure as AI Consulting — à la carte or on retainer.',
                'bullets' => [
                    '$95/hr à la carte',
                    'Starter retainer: $1000/mo for up to 10 hrs',
                    'Growth retainer: $2000/mo for up to 25 hrs',
                ],
            ],
            [
                'slug' => 'website-design',
                'title' => 'Website Design',
                'card_excerpt' => 'Fast, content-driven sites paired with the same AI-assisted workflows we build for clients — built to launch quickly and evolve without a redesign.',
                'hero_subheading' => 'Fast, content-driven sites paired with the same AI-assisted workflows we build for clients.',
                'section_heading' => 'Packages',
                'body' => 'Four tiers, escalating from a static brochure site to a full dynamic build. All tiers include a $20/mo hosting/maintenance fee on top of the one-time build price.',
                'bullets' => [
                    '$500 — static one-page brochure site, SEO-optimized, contact-collection CTA, customized to your brand',
                    '$1500 — multi-page static site + a basic chatbot trained on your company data + 1 automation',
                    '$2500 — up to 10-page dynamic site, database-backed CMS, custom design, chatbot + AI-assisted CMS content generation, up to 2 automations',
                    '$5000 — 10+ page fully dynamic site, custom design, product catalogue/e-commerce if needed, up to 3 automations',
                ],
            ],
            [
                'slug' => 'voice-ai-receptionists',
                'title' => 'Voice AI Receptionists',
                'card_excerpt' => 'A real phone line, answered by an AI receptionist trained on your business — conversation flow, voice, and data, built around how you actually work.',
                'hero_subheading' => 'A real phone line, answered by an AI receptionist trained on your business.',
                'section_heading' => 'Pricing',
                'body' => 'Sold separately from the website packages above.',
                'bullets' => [
                    '$950 setup — conversation flow design, voice character, training on your data',
                    '$500/mo ongoing',
                ],
            ],
        ];

        foreach ($services as $sortOrder => $service) {
            Page::query()->updateOrCreate(
                ['parent_id' => $servicesIndex->id, 'slug' => $service['slug']],
                [
                    'title' => $service['title'],
                    'template' => PageTemplate::Service,
                    'is_published' => true,
                    'show_in_nav' => false,
                    'sort_order' => $sortOrder,
                    'card_excerpt' => $service['card_excerpt'],
                    'hero_subheading' => $service['hero_subheading'],
                    'section_heading' => $service['section_heading'],
                    'body' => $service['body'],
                    'bullets' => $service['bullets'],
                    'cta_eyebrow' => 'Ready When You Are',
                    'cta_heading' => "Let's talk about your project",
                    'cta_button_label' => 'Start a Project',
                ]
            );
        }

        Page::query()->updateOrCreate(
            ['parent_id' => null, 'slug' => 'approach'],
            [
                'title' => 'Approach',
                'template' => PageTemplate::Standard,
                'is_published' => true,
                'show_in_nav' => true,
                'nav_anchor' => 'approach',
                'section_eyebrow' => 'Approach',
                'hero_heading' => "Static where it's simple, AI where it earns its keep",
                'hero_subheading' => 'Most of what a business needs from a website is speed and clarity. The AI does the actual work behind it.',
                'section_heading' => 'The stack behind it',
                'body' => 'The AI does the actual work — qualifying leads, answering questions, and automating the busywork between the tools you already use — while the frontend stays fast, simple, and cheap to run.',
                'bullets' => [
                    'n8n as the automation and integration layer between your existing tools',
                    'Custom chat and RAG assistants trained on your own content',
                    'Static-first frontends that stay fast and cheap to run',
                ],
                'cta_eyebrow' => 'Ready When You Are',
                'cta_heading' => "Let's talk about your project",
                'cta_button_label' => 'Start a Project',
            ]
        );

        Page::query()->updateOrCreate(
            ['parent_id' => null, 'slug' => 'about'],
            [
                'title' => 'About',
                'template' => PageTemplate::Standard,
                'is_published' => true,
                'show_in_nav' => false,
                'section_eyebrow' => 'About',
                'hero_heading' => 'About AH Media',
                'hero_subheading' => 'AH Media is built and run by Adam Haley, who designs and ships the AI systems behind this site — the chat widget, the automation, and the static-site toolkit it\'s built on.',
                'section_heading' => "Why It's Different",
                'body' => 'No account managers, no hand-off between the person who scopes the project and the person who builds it.',
                'bullets' => [
                    'Direct access to the person actually building your system',
                    'AI and automation work, not just strategy slides',
                    'Built using the same static-first + AI toolkit used on this site',
                ],
                'cta_eyebrow' => 'Ready When You Are',
                'cta_heading' => "Let's talk about your project",
                'cta_button_label' => 'Start a Project',
            ]
        );

        Page::query()->updateOrCreate(
            ['parent_id' => null, 'slug' => 'case-studies'],
            [
                'title' => 'Case Studies',
                'template' => PageTemplate::Standard,
                'is_published' => true,
                'show_in_nav' => true,
                'nav_anchor' => 'case-studies',
                'section_eyebrow' => 'Case Studies',
                'hero_heading' => 'Real work, coming soon',
                'hero_subheading' => "We're building out detailed case studies from current engagements. Check back soon — or get in touch to talk through a project directly.",
                'cta_eyebrow' => 'Ready When You Are',
                'cta_heading' => "Let's talk about your project",
                'cta_button_label' => 'Start a Project',
            ]
        );

        Page::query()->updateOrCreate(
            ['parent_id' => null, 'slug' => 'contact'],
            [
                'title' => 'Contact',
                'template' => PageTemplate::Contact,
                'is_published' => true,
                'show_in_nav' => true,
                'nav_anchor' => 'contact',
                'section_eyebrow' => 'Contact',
                'hero_heading' => "Let's talk about what you're trying to build",
                'hero_subheading' => "Send a few details and we'll get back to you — or use the chat in the corner for something quicker.",
            ]
        );
    }
}
