<?php

use App\Models\FaqItem;
use App\Models\Page;
use Illuminate\Database\Migrations\Migration;

/**
 * Takes dollar figures out of the FAQs and service pages so the site informs and invites a
 * conversation instead of leading with price. Website package prices stay on the Web Design &
 * Branding page. Page copy changes are exact-string replacements, so passages already edited
 * in the admin are left alone.
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach ([
            'How much do you charge?' => '<p>It depends on what you need, so we quote each project once we understand it. Websites are the exception: they come in fixed-price packages, listed on <a href="/services/web-design-and-branding">Web Design &amp; Branding</a>.</p><p>For anything else, <a href="/contact">send us a message</a>. It\'s free, and we\'ll come back with an honest take and a clear quote.</p>',
            'Do you offer monthly retainers?' => '<p>Yes. Retainers are monthly blocks of hours for ongoing automation, consulting and support after a project launches. <a href="/contact">Get in touch</a> and we\'ll suggest the size that fits.</p>',
            'How much does a website cost?' => '<p>Websites come in four fixed-price packages, from a one-page site to a full custom build, all with hosting and maintenance. Every package includes SEO and setup of your Google Business Profile. Compare them on <a href="/services/web-design-and-branding">Web Design &amp; Branding</a>.</p>',
            'What is an AI voice receptionist?' => '<p>A real phone line answered by an AI receptionist trained on your business. It picks up on the first ring, around the clock, answers common questions, takes messages and books consultations. The phone number and accounts stay in your business\'s name.</p><p>Hear it for yourself: call our demo receptionist at <a href="tel:+17653075537">(765) 307-5537</a>. See <a href="/services/voice-ai-receptionists">Voice AI Receptionists</a>, or <a href="/contact">get in touch</a> to talk through your calls.</p>',
            'What is the Brand Audit for AI-built sites?' => '<p>AI website builders make every site look the same. The Brand Audit reviews up to five pages across six layers (type, colour, hero, layout, header and icons, and copy), rates each one, and gives you a fix plan ranked by effort and impact.</p><p>It takes 5 days, and we can make the changes for you too.</p>',
            'What is the Vibe-Coded App Go-Live Review?' => '<p>If you built an app with an AI tool and are about to put real users on it, the 360° Go-Live Review checks every side of it first: security, data isolation, secrets, code quality, maintainability, performance, reliability and launch readiness. Every critical and high issue is fixed, and you get a plain-English report.</p><p>It takes 5 days, with an optional codebase clean-up.</p>',
            'My website has been hacked. Can you help?' => '<p>Yes. Hacked Site Recovery finds how they got in, removes the malware, backdoors and rogue accounts, hardens the site and asks browsers to lift any "deceptive site" warnings. You get a plain-English incident report and 30 days of cover if the same way in is used again.</p><p>It\'s done in 3 days, or 24 hours if it\'s urgent. WordPress is the most common case, but we work on any site we can get server access to.</p>',
        ] as $question => $answer) {
            FaqItem::query()->where('question', $question)->update(['answer' => $answer]);
        }

        $this->replaceInBody('services/automations', [
            "## Pricing\n\nHourly, or on a monthly retainer." => "## Working together\n\nBy the hour, or on a monthly retainer for ongoing work. [Tell us what you'd like to automate](/contact) and we'll come back with an honest take and a clear quote.",
        ], clearBullets: true);

        $this->replaceInBody('services/voice-ai-receptionists', [
            "That's \$450 a week, more than \$23,000 a year, against \$6,000 a year (plus setup and call usage) for a receptionist that never misses." => "That's \$450 a week, more than \$23,000 a year, walking out the door.",
            "## Pricing\n\nSold separately from the website packages. Call usage is billed directly to the accounts in your name, typically 10 to 15 cents a minute for the AI voice and phone line together, with no markup from us. We estimate yours from your call volume on the first call." => "## Getting started\n\nSold separately from the website packages. Setup and ongoing management are quoted once we know your call volume, and call usage is billed directly to the accounts in your name with no markup from us. [Get in touch](/contact) to talk it through.",
        ], clearBullets: true);

        $this->replaceInBody('services/prism-cms', [
            'our $2,500 and $5,000 website packages' => 'our CMS Site and Full Custom Build website packages',
        ]);

        Page::query()->where('path', 'services/consultations')->where('section_heading', 'Pricing')->first()?->update([
            'section_heading' => 'How it works',
            'bullets' => [
                'Free first message: tell us what\'s going on',
                'A 30 or 60-minute call to map it out',
                'A written plan with clear costs',
                'Retainers for ongoing work',
            ],
        ]);
    }

    /** @param array<string, string> $replacements */
    private function replaceInBody(string $path, array $replacements, bool $clearBullets = false): void
    {
        $page = Page::query()->where('path', $path)->first();
        if (! $page) {
            return;
        }

        $page->update([
            'body' => str_replace(array_keys($replacements), array_values($replacements), (string) $page->body),
            ...($clearBullets ? ['bullets' => null] : []),
        ]);
    }
};
