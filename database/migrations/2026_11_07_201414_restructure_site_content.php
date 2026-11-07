<?php

use App\Data\DopplerRingSettings;
use App\Enums\PageTemplate;
use App\Models\CaseStudy;
use App\Models\Credential;
use App\Models\DopplerRingStyle;
use App\Models\FaqItem;
use App\Models\Package;
use App\Models\Page;
use App\Models\SiteSetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Storage;

/**
 * One-off content update for the 2026-11-06 site restructure (llm-wiki
 * projects/ahmedia/plans/2026-11-06.md). Content lives in the CMS, so this
 * runs once on deploy; each step skips quietly when its target is missing.
 */
return new class extends Migration
{
    public function up(): void
    {
        // A fresh database (tests, a new environment) gets its content from the seeders
        if (! Page::query()->exists()) {
            return;
        }

        SiteSetting::current()->update([
            'footer_tagline' => 'Use AI, Stay Human',
            'address' => '5161 Lankershim Blvd, North Hollywood, CA 91601',
        ]);

        $this->updatePage('', [
            'sort_order' => 0,
            'buttons' => [['label' => 'Meet Adam', 'url' => '/about', 'style' => 'secondary']],
        ]);

        $this->updatePage('about', [
            'template' => PageTemplate::About,
            'sort_order' => 1,
            'show_in_nav' => true,
            'hero_heading' => 'About AH Media',
            'hero_subheading' => 'One senior developer, twenty-plus years of shipping software, and a simple rule for AI: use it, stay human.',
            'section_eyebrow' => 'About',
            'section_heading' => 'Adam Haley',
            'body' => <<<'MD'
                AH Media is built and run by me, Adam Haley. I've been building software for more than twenty years, from production tracking systems at Walt Disney Television Animation to contract work for Apple, along with Sony and a long run of studios and agencies in Los Angeles.

                These days I build the AI systems small businesses actually run on: n8n automations, chat and voice assistants trained on your own content, second brains, and fast, CMS-driven websites. You work directly with the person building your system. No account managers, no hand-offs.

                ## Use AI, stay human

                AI and automation shouldn't erase the human touch points in your business. They should let you design exactly what those touch points look like. I start with what already works, leave it alone, and go to work on whatever is getting in the way. Everything I install runs with your oversight and approval.

                ## About the rings

                The AH Media mark is a set of propagating rings: fixed rules, sampled with seeded randomness, so every version varies but always coheres. That's how an LLM works too. The logo doesn't just look like AI, it works like it. You can play with it in [the Lab](/lab).
                MD,
            'bullets' => [
                'Direct access to the person actually building your system',
                'Top Rated Plus on Upwork, with a 100% Job Success Score',
                'Certified in n8n workflow automation',
            ],
        ]);

        $this->updatePage('lab', ['sort_order' => 2]);

        $this->updatePage('services', [
            'sort_order' => 3,
            'hero_subheading' => 'Agentic systems, brand expansion and consulting. Pick a starting point, or talk through where you actually are.',
            'section_heading' => 'Agentic Systems, Brand Expansion, Consulting',
            'body' => 'Each engagement starts with a real bottleneck, not a technology mandate.',
        ]);

        $this->updateServices();

        $this->updatePage('case-studies', [
            'sort_order' => 4,
            'is_published' => true,
            'show_in_nav' => true,
            'hero_heading' => 'Case Studies & Testimonials',
            'hero_subheading' => 'Selected projects, and what the people behind them had to say.',
            'section_eyebrow' => 'Case Studies',
        ]);

        CaseStudy::query()->where('slug', 'monk-magazine-hacked-site-recovery')->update(['tags' => json_encode(['Web Development'])]);
        CaseStudy::query()->where('slug', 'dream-world-cirque-brand-pass')->update(['tags' => json_encode(['Branding', 'Web Development'])]);

        // Unpublished until the first post is ready; publishing it adds Blog to the nav
        Page::query()->firstOrCreate(['path' => 'blog'], [
            'title' => 'Blog',
            'slug' => 'blog',
            'template' => PageTemplate::Blog,
            'sort_order' => 5,
            'is_published' => false,
            'show_in_nav' => true,
            'hero_heading' => 'The Agentic Age',
            'hero_subheading' => 'Notes on building with AI while keeping the human in the loop.',
            'section_eyebrow' => 'Blog',
            'cta_eyebrow' => 'Ready When You Are',
            'cta_heading' => "Let's talk about your project",
            'cta_button_label' => 'Start a Project',
        ]);

        $this->updatePage('faq', ['sort_order' => 6, 'show_in_nav' => true]);
        $this->updatePage('contact', ['sort_order' => 7]);
        $this->updatePage('approach', ['sort_order' => 8]);

        $this->addCredentials();
        $this->updateFaqs();
    }

    private function updateServices(): void
    {
        $this->updatePage('services/custom-programming', [
            'title' => 'Automations',
            'slug' => 'automations',
            'sort_order' => 0,
            'hero_subheading' => 'n8n workflows that connect the tools you already run on, and a clear path for when you outgrow them.',
            'card_excerpt' => 'n8n workflows that connect the tools you already use, built by a certified n8n developer, with a migration path for when you grow beyond them.',
            'section_heading' => 'Automation with n8n',
            'body' => <<<'MD'
                Most of the busywork in a small business lives between tools: a form fills in, someone copies it into the CRM, someone else sends the follow-up. We automate those hand-offs with [n8n](https://n8n.io), an open workflow platform with hundreds of integrations, and custom code where it earns its keep.

                ## Example workflows

                - **Lead intake:** contact form and chat leads are screened for spam and routed to the right inbox or CRM.
                - **Job and prospect feeds:** new postings are pulled, scored against your criteria, de-duplicated and sent to Telegram or Slack.
                - **Voice capture:** a voice note on your phone becomes a transcribed, tagged idea in your notes or spreadsheet.
                - **Website audits:** screenshots, an AI review and a PDF report, generated from a single URL.

                ## Why n8n

                | | n8n | Zapier / Make | Custom code |
                |---|---|---|---|
                | Pricing | Self-hosted, or flat cloud plans | Per task, climbs with volume | Developer time |
                | Your data | Can stay on your own server | On their platform | Wherever you put it |
                | AI and agents | Built in (LLM, agent and vector nodes) | Add-ons | Anything, by hand |
                | Changes | Visual, readable by non-developers | Visual | Needs a developer |

                n8n gives you most of the flexibility of code with a workflow anyone on your team can read. It plays well with the rest of your stack too: it can call your own APIs, run scripts, hand off to Zapier where you already use it, and trigger from webhooks, schedules or email.

                ## When you outgrow it

                n8n isn't the answer forever, and we'll tell you when it stops being one. Common next steps:

                - **Scripts on a schedule:** high-volume, stable jobs move to small scripts run by cron, cheaper and easier to test.
                - **Durable workflows:** long-running, business-critical processes move to an engine like [Temporal](https://temporal.io), with retries and history built in.
                - **Native automations:** when a platform you rely on adds the feature itself (your CRM, help desk or store), we move the workflow there and retire ours.

                ## Pricing

                Hourly, or on a monthly retainer.
                MD,
            'bullets' => [
                '$95/hr à la carte',
                'Starter retainer: $1,000/mo for up to 10 hrs',
                'Growth retainer: $2,000/mo for up to 25 hrs',
            ],
        ]);

        $this->createService('second-brain', [
            'title' => 'Second Brain',
            'sort_order' => 1,
            'hero_subheading' => 'Your own agentic OS: a folder of plain files, LLM agents that read and write them, and scripts that keep it honest.',
            'card_excerpt' => 'A folder of plain files, LLM agents that work in it, and deterministic scripts that keep it honest: your own agentic operating system.',
            'section_heading' => 'Files + LLMs + scripts = your Second Brain',
            'body' => <<<'MD'
                A second brain is where your business remembers things: plans, decisions, clients, notes, and what happened last Tuesday. Ours are built from three simple parts, so they're cheap to run, easy to change, and never locked in to one vendor.

                ![How a second brain fits together: Human:IO, your files, LLM agents, and scripts and hooks](/images/second-brain-diagram.svg)

                ## The Human:IO layer

                The part you touch. Talk to it with a voice note from your phone, chat with it in your terminal or browser, or read and edit it in [Obsidian](https://obsidian.md). Each morning it can hand you a short brief of open to-dos and unfinished plans. You never have to learn where things are filed; the agents do the filing.

                ## Your files

                Everything lives in a folder of plain Markdown files with a little structured data at the top: one folder per project, with its plans, session logs and lessons learned, plus people, patterns and references. Plain files mean you own them, they sync anywhere, and any tool, human or AI, can read them.

                ## LLM agents

                Agents like Claude Code work inside the folder. **Context files** tell them how your business works, **skills** teach them repeatable jobs (log the session, start a project, record a takeaway), and **memory** carries what they learned from one session to the next.

                ## Scripts and hooks

                Anything that must come out the same every time is a script, not a prompt. Hooks run at the start and end of each session to brief you and log the work; small edit scripts check off to-dos and update plan status without ever mangling a file; n8n workflows bring in voice notes and other outside input.

                ## What you get

                A working second brain set up around how you already work, the context files and skills to run it, and a walkthrough so you can extend it yourself.
                MD,
            'bullets' => [
                'Setup and onboarding: quoted after a consultation',
                'Ongoing help on the retainers under Automations',
            ],
        ]);

        $this->updatePage('services/website-design', [
            'title' => 'Web Design & Branding',
            'slug' => 'web-design-and-branding',
            'sort_order' => 2,
            'hero_subheading' => 'Fast, content-driven websites and a brand that looks like you, not like every other AI-built site.',
            'card_excerpt' => 'Websites from $500 to $5,000, with SEO and a Google Business Profile included, and branding that doesn\'t look AI-generated.',
            'section_heading' => 'What every site includes',
            'body' => <<<'MD'
                Every website includes an **SEO treatment** (page titles, descriptions, social sharing images, structured data and a sitemap) and **setup of your Google Business Profile**, so you show up on Google Maps and local search from day one. Each package has a $20/mo hosting and maintenance fee on top of the one-time price.

                ## Branding

                Generic is the default for AI-built sites. We start from what makes you specific and build a visual identity around it, often generative: a mark made from rules and seeded variation rather than a single drawing, like the rings in our own logo.
                MD,
            'bullets' => null,
        ]);

        $this->addWebsitePackages();

        $this->createService('prism-cms', [
            'title' => 'Prism CMS',
            'sort_order' => 3,
            'hero_subheading' => 'WordPress for the Agentic Age: a fast, secure CMS with AI built in, not bolted on.',
            'card_excerpt' => 'WordPress for the Agentic Age: a CMS with AI built in, no plugin roulette, and a site you can edit yourself.',
            'section_heading' => 'WordPress for the Agentic Age',
            'body' => <<<'MD'
                Prism CMS is the system this site runs on. It's built on Laravel and Filament, so you get a clean admin to edit pages, services, case studies, FAQs and blog posts, without the plugin sprawl and constant security patching that come with WordPress.

                ## AI built in

                - **An FAQ chatbot** that answers from your own content, with no per-message AI bill.
                - **Spam filtering** that classifies every contact form submission and only emails you the real leads.
                - **Leads in one place:** contact form and chatbot leads land in the same inbox in your admin.

                ## Why not WordPress?

                Hacked WordPress sites usually trace back to an outdated plugin or theme. Prism CMS has no third-party plugins to keep patched, and no widgets that load other companies' scripts and cookies on your pages. It's also fast by default.

                ## Who it's for

                Businesses that want to edit their own site and grow into AI features over time. Prism CMS is the platform behind our $2,500 and $5,000 website packages, and can be extended with custom features.
                MD,
            'bullets' => null,
        ]);

        $this->updatePage('services/voice-ai-receptionists', [
            'sort_order' => 4,
            'hero_subheading' => 'A real phone line, answered by an AI receptionist trained on your business. Call the demo: (765) 307-5537.',
            'section_heading' => 'Never miss a call',
            'body' => <<<'MD'
                Your AI receptionist answers on a real phone number, around the clock, on the first ring. It greets callers in a voice that fits your brand, answers questions from your own information (hours, services, prices, policies), takes messages, and books consultations or routes calls the way you would.

                ## Hear it for yourself

                Call our demo receptionist at **[(765) 307-5537](tel:+17653075537)**. She answers for a sample med spa and can book you a consultation. Yours would be trained on your own business.

                ## What a missed call costs

                Every call that goes to voicemail is a customer who might call the next business on the list. The math is simple:

                **missed calls a week × average job value × how often a call books = revenue walking away**

                Say you miss 5 calls a week, an average booking is worth $300, and 3 in 10 callers book. That's $450 a week, more than $23,000 a year, against $6,000 a year (plus setup and call usage) for a receptionist that never misses. We run this with your real numbers on the first call.

                ## You own everything

                Your phone number, phone account and AI voice account are all in your business's name, on your own card, not ours. We set them up, connect them, and walk you through the carrier registration that keeps your calls from being flagged as spam. We manage and tune your receptionist with access you grant, and if you ever leave, all of it goes with you.

                ## How it works

                1. **Discovery:** a call about your business, what callers usually ask, and roughly how many calls you get on a busy day.
                2. **Conversation design:** we script how she greets, answers, books and hands off, and pick a voice that fits your brand.
                3. **Training:** she learns your services, prices, hours and policies from your own information.
                4. **Go live:** we connect your number and test it with you before real callers reach her.
                5. **Tuning:** we review real calls and keep tightening her answers.

                ## Connects to the rest of your business

                Pair her with [Automations](/services/automations) to send call summaries to your inbox, log callers in your CRM, or follow up by email after a booking.

                ## Who it's for

                Med spas, clinics, salons and service businesses where a missed call is a missed booking, especially when the front desk is busy with the people already in the room.

                ## Pricing

                Sold separately from the website packages. Call usage is billed directly to the accounts in your name, typically 10 to 15 cents a minute for the AI voice and phone line together, with no markup from us. We estimate yours from your call volume on the first call.
                MD,
            'bullets' => [
                '$950 setup: conversation design, voice character and training on your data',
                '$500/mo for management and tuning from real calls',
            ],
        ]);

        $this->updatePage('services/ai-consulting', [
            'title' => 'Consultations',
            'slug' => 'consultations',
            'sort_order' => 5,
            'hero_subheading' => 'Talk it through with a senior developer before you spend money on the wrong tool.',
            'card_excerpt' => 'Strategy, opportunity mapping and a clear path to production, not just a slide deck. Book a call, or keep us on retainer.',
            'section_heading' => 'Pricing',
            'body' => "Message us first, free: tell us what's eating your week and we'll tell you honestly whether we can help. Then a paid call maps it out and gives you a plan with costs. Or keep us on retainer for ongoing work.",
            'bullets' => [
                '30-minute consultation: $50',
                '60-minute consultation: $100',
                '$95/hr à la carte',
                'Starter retainer: $1,000/mo for up to 10 hrs of consulting or automation work',
                'Growth retainer: $2,000/mo for up to 25 hrs of consulting or automation work',
            ],
        ]);

        // Kept in the CMS, off the site for now
        $this->updatePage('services/security-and-launch', ['sort_order' => 6, 'is_published' => false]);

        $this->addServiceRingStyles();
    }

    /**
     * Each service gets its own rings: the Services style with spacing and size jitter maxed
     * (where every seed looks good), varied only by seed.
     */
    private function addServiceRingStyles(): void
    {
        $base = DopplerRingStyle::query()->where('name', 'Services')->first()?->settings;
        if (! $base) {
            return;
        }

        $seeds = [
            'automations' => 113,
            'second-brain' => 242,
            'web-design-and-branding' => 371,
            'prism-cms' => 486,
            'voice-ai-receptionists' => 529,
            'consultations' => 657,
            'security-and-launch' => 798,
        ];

        foreach ($seeds as $slug => $seed) {
            $page = Page::query()->where('path', 'services/'.$slug)->first();
            if (! $page || $page->doppler_ring_style_id || $page->hide_doppler_rings) {
                continue;
            }

            $style = DopplerRingStyle::query()->firstOrCreate(['name' => 'Service: '.$page->title], [
                'settings' => DopplerRingSettings::fromArray([...$base->toArray(), 'spacing' => 1, 'size' => 1, 'seed' => $seed, 'motion' => 'still']),
            ]);
            $page->update(['doppler_ring_style_id' => $style->id]);
        }
    }

    private function addWebsitePackages(): void
    {
        $page = Page::query()->where('path', 'services/web-design-and-branding')->first();
        if (! $page) {
            return;
        }

        $hosting = 'Plus $20/mo hosting and maintenance';
        $packages = [
            ['One-Page Site', 'one-page-site', 500, 'A fast, single-page brochure site that tells visitors who you are and gets them in touch.', [
                'One page, customized to your brand',
                'Contact form that collects leads',
                'SEO treatment and Google Business Profile setup',
                $hosting,
            ]],
            ['Multi-Page Site', 'multi-page-site', 1500, 'Room for your services, story and contact details, plus a chatbot that answers visitors\' questions.', [
                'Multiple pages, customized to your brand',
                'A basic chatbot trained on your company data',
                'One automation (for example, routing new leads)',
                'SEO treatment and Google Business Profile setup',
                $hosting,
            ]],
            ['CMS Site', 'cms-site', 2500, 'A custom-designed site on Prism CMS that you edit yourself, with AI helping you write.', [
                'Up to 10 pages with a custom design',
                'Prism CMS, so you can edit your own content',
                'A chatbot plus AI-assisted content writing in the CMS',
                'Up to two automations',
                'SEO treatment and Google Business Profile setup',
                $hosting,
            ]],
            ['Full Custom Build', 'full-custom-build', 5000, 'A fully dynamic, custom site for businesses with more to show and sell.', [
                '10+ pages with a custom design',
                'Fully dynamic, on Prism CMS',
                'Product catalog or e-commerce if you need it',
                'Up to three automations',
                'SEO treatment and Google Business Profile setup',
                $hosting,
            ]],
        ];

        foreach ($packages as $sortOrder => [$title, $slug, $price, $summary, $includes]) {
            Package::query()->firstOrCreate(['page_id' => $page->id, 'slug' => $slug], [
                'title' => $title,
                'price' => $price,
                'summary' => $summary,
                'includes' => $includes,
                'is_published' => true,
                'sort_order' => $sortOrder,
            ]);
        }

        // The brand audit follows the website packages
        Package::query()->where('page_id', $page->id)->where('slug', 'brand-audit')->update(['sort_order' => count($packages)]);
    }

    /** @param array<string, mixed> $attributes */
    private function updatePage(string $path, array $attributes): void
    {
        Page::query()->where('path', $path)->first()?->update($attributes);
    }

    /** @param array<string, mixed> $attributes */
    private function createService(string $slug, array $attributes): void
    {
        $services = Page::query()->where('path', 'services')->first();
        if (! $services) {
            return;
        }

        Page::query()->firstOrCreate(['path' => 'services/'.$slug], [
            'parent_id' => $services->id,
            'slug' => $slug,
            'template' => PageTemplate::Service,
            'is_published' => true,
            'cta_eyebrow' => 'Ready When You Are',
            'cta_heading' => "Let's talk about your project",
            'cta_button_label' => 'Start a Project',
            ...$attributes,
        ]);
    }

    private function addCredentials(): void
    {
        if (Credential::query()->exists()) {
            return;
        }

        $upwork = 'https://www.upwork.com/freelancers/~01f89cc7b2830c8fa4';

        // [name, issuer, stat, issued on, link, badge, featured]
        $credentials = [
            ['Top Rated Plus', 'Upwork', null, null, $upwork, null, true],
            ['Job Success Score', 'Upwork', '100%', null, $upwork, null, true],
            ['Jobs completed', 'Upwork', '26', null, $upwork, null, true],
            ['Years building software', 'Disney, Sony, Apple and more', '20+', null, null, null, true],
            ['n8n Foundations Pathway', 'n8n', null, '2026-10-30', 'https://badges.n8n.io/921404a3-158e-4a46-9b11-edfd594ec66a', 'n8n-foundations-pathway.png', false],
            ['N8N101 Essentials: Your First Workflows', 'n8n', null, '2026-10-01', 'https://badges.n8n.io/758e6aa7-7925-43ad-9db7-695e82dcf4c9', 'n8n-101.png', false],
            ['N8N102 Integrations: APIs & Connected Workflows', 'n8n', null, '2026-10-01', 'https://badges.n8n.io/5c308c20-01b4-4b52-8a4d-d65615e06531', 'n8n-102.png', false],
            ['N8N103 In Practice: AI, Testing & Best Practices', 'n8n', null, '2026-10-01', 'https://learn.n8n.io/certificates/a854383223484b5a9b2405be345205b5', 'n8n-103.png', false],
            ['Google Prompting Essentials', 'Google (Coursera)', null, '2025-04-17', null, null, false],
        ];

        foreach ($credentials as $sortOrder => [$name, $issuer, $value, $issuedOn, $url, $badge, $isFeatured]) {
            $badgePath = null;
            if ($badge) {
                $badgePath = 'credentials/'.$badge;
                Storage::disk('public')->put($badgePath, file_get_contents(database_path('seeders/content/'.$badgePath)));
            }

            Credential::query()->create([
                'name' => $name,
                'issuer' => $issuer,
                'value' => $value,
                'issued_on' => $issuedOn,
                'url' => $url,
                'badge_image' => $badgePath,
                'is_featured' => $isFeatured,
                'sort_order' => $sortOrder,
            ]);
        }
    }

    private function updateFaqs(): void
    {
        FaqItem::query()->where('question', 'What does AH Media do?')->update([
            'answer' => '<p>AH Media designs, builds and ships the AI systems businesses actually run on: n8n automations that connect the tools you already use, second brains (your own agentic OS), chat and voice assistants trained on your own content, and fast websites on Prism CMS, our AI-ready CMS.</p><p>We also offer branding, consultations and fixed-price packages for securing vibe-coded apps before launch and recovering hacked sites.</p>',
        ]);

        FaqItem::query()->where('question', 'How much does a website cost?')->update([
            'answer' => '<p>Websites come in four fixed-price packages, each with a $20 a month hosting and maintenance fee:</p><ul><li>$500: a one-page site with a contact form</li><li>$1,500: a multi-page site plus a basic chatbot trained on your company data and one automation</li><li>$2,500: up to 10 pages on Prism CMS, which you can edit yourself, custom design, a chatbot with AI-assisted content and up to two automations</li><li>$5,000: 10+ pages, fully dynamic, a product catalog or e-commerce if needed, and up to three automations</li></ul><p>Every package includes SEO and setup of your Google Business Profile. See <a href="/services/web-design-and-branding">Web Design &amp; Branding</a>.</p>',
        ]);

        FaqItem::query()->where('question', 'What is an AI voice receptionist?')->update([
            'answer' => '<p>A real phone line answered by an AI receptionist trained on your business. It picks up on the first ring, around the clock, answers common questions, takes messages and books consultations. The phone number and accounts stay in your business\'s name.</p><p>Set-up is $950, then $500 a month for management and tuning, plus call usage billed directly to your own accounts (typically 10 to 15 cents a minute). Hear it for yourself: call our demo receptionist at <a href="tel:+17653075537">(765) 307-5537</a>. See <a href="/services/voice-ai-receptionists">Voice AI Receptionists</a>.</p>',
            'keywords' => 'phone, voice, receptionist, calls, answering service, voice ai, call answering, bookings, appointments, missed calls, demo, phone number, front desk, voicemail',
        ]);

        $nextSortOrder = (int) FaqItem::query()->max('sort_order') + 1;

        FaqItem::query()->firstOrCreate(['question' => 'What is a second brain?'], [
            'answer' => '<p>A second brain is where your business remembers things: a folder of plain Markdown files (projects, plans, people, notes), AI agents that read and write it for you, and small scripts that handle the steps that must come out the same every time. You talk to it by voice, chat or Obsidian. See <a href="/services/second-brain">Second Brain</a>.</p>',
            'keywords' => 'second brain, agentic os, knowledge base, notes, obsidian, memory, personal knowledge management, pkm, claude code, organize',
            'sort_order' => $nextSortOrder,
        ]);

        FaqItem::query()->firstOrCreate(['question' => 'What is Prism CMS?'], [
            'answer' => '<p>Prism CMS is the content management system this site runs on: WordPress for the Agentic Age. You edit your own pages, services and blog, with an FAQ chatbot and spam filtering built in, and no third-party plugins to keep patched. See <a href="/services/prism-cms">Prism CMS</a>.</p>',
            'keywords' => 'prism, prism cms, cms, wordpress, wordpress alternative, edit my site, content management, laravel, plugins',
            'sort_order' => $nextSortOrder + 1,
        ]);
    }
};
