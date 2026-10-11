<?php

namespace Database\Seeders;

use App\Models\CaseStudy;
use App\Models\Package;
use App\Models\Page;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Seeds the fixed-price packages and the case studies that back them.
 * Copy and pricing are transcribed from the second brain
 * (wiki/projects/ahmedia/reference.md), which stays the source of truth;
 * images and sample PDFs live in database/seeders/content/ and are copied
 * to the public disk so a production `db:seed` reproduces them.
 */
class PackageAndCaseStudyContentSeeder extends Seeder
{
    public function run(): void
    {
        $websiteDesign = Page::query()->where('path', 'services/website-design')->firstOrFail();
        $securityAndLaunch = Page::query()->where('path', 'services/security-and-launch')->firstOrFail();

        $brandAudit = $this->package($websiteDesign, 0, [
            'slug' => 'brand-audit',
            'title' => 'Brand Audit for AI-Built Sites',
            'summary' => 'AI builders make every site look the same. We audit yours layer by layer and show you exactly how to make it unmistakably yours.',
            'price' => 500,
            'delivery_days' => 5,
            'includes' => [
                'Audit of up to 5 pages across six layers: type, colour, hero, layout, header and icons, and copy',
                'Each layer rated Generic, Partly or Distinct, with what to change and why',
                'A check that what visitors actually see matches your design (fonts that never load, for one)',
                'A branded PDF report with a fix plan ranked by effort and impact',
                'One round of revisions',
            ],
            'add_ons' => [
                ['title' => 'Implement the recommended changes', 'price' => 700, 'days' => 5, 'description' => 'We apply the priority-plan fixes to your site: type, colour, hero, layout, header and icons, and copy.'],
            ],
            'sample_document' => 'brand-audit-sample.pdf',
            'upwork_url' => 'https://www.upwork.com/services/product/2109067449738019986',
        ]);

        $this->package($securityAndLaunch, 0, [
            'slug' => 'vibe-coded-go-live-review',
            'title' => 'Vibe-Coded App Go-Live Review',
            'summary' => "You built it fast. Our 360° Go-Live Review checks every side of your app before real users arrive, then we fix what blocks launch. You don't know what you don't know; we do.",
            'price' => 1000,
            'delivery_days' => 5,
            'includes' => [
                'The full 360° Go-Live Review: security, data isolation, secrets and config, code quality, maintainability, performance, reliability and launch readiness',
                'Every critical and high issue fixed and tested, from keys in the browser to users seeing each other’s data',
                'A plain-English report with every finding, every fix and a ranked plan for the rest',
                'Hosting set up for you if you don’t have it yet',
            ],
            'add_ons' => [
                ['title' => 'Codebase clean-up', 'price' => 750, 'days' => 5, 'description' => 'Duplicated logic consolidated, configuration moved out of the code and into the environment, and tests on your core flows, so the next developer can build on it.'],
            ],
            'sample_document' => 'golive-review-sample.pdf',
            'upwork_url' => 'https://www.upwork.com/services/product/2109066654547305880',
        ]);

        $hackedSiteRecovery = $this->package($securityAndLaunch, 1, [
            'slug' => 'hacked-site-recovery',
            'title' => 'Hacked Site Recovery',
            'summary' => 'Red browser warning, strange redirects, a site acting oddly? We find how they got in, remove every way back in and lock it down. WordPress or otherwise.',
            'price' => 1000,
            'delivery_days' => 3,
            'includes' => [
                'Triage: what is compromised and what isn’t, and an immediate stop to what visitors see',
                'Malware, backdoors, rogue admin accounts and scheduled tasks removed, with a second sweep',
                'Server log review to find the entry point and anything the sweeps missed',
                'Hardening plus a daily integrity check that emails you if files change',
                'A plain-English incident report and requests to browsers to lift their warnings',
                '30-day cover: if the same way in is used again, we clean it up at no charge',
            ],
            'add_ons' => [
                ['title' => 'Clean rebuild on fresh hosting', 'price' => 750, 'days' => 3, 'description' => 'For deep infections: rebuilt from trusted sources on new, isolated hosting, database cleaned, every media file checked, tested before switching over.'],
                ['title' => 'Rush: full clean-up within 24 hours', 'price' => 300, 'days' => null, 'description' => 'For when every hour of the warning costs you customers.'],
            ],
            'sample_document' => 'recovery-report-sample.pdf',
            'upwork_url' => 'https://www.upwork.com/services/product/2109401338180355218',
        ]);

        $this->caseStudy(0, $brandAudit, [
            'slug' => 'dream-world-cirque-brand-pass',
            'title' => 'Dream World Cirque: a brand pass',
            'client_name' => 'Dream World Cirque',
            'external_link' => 'https://dreamworldcirque.com',
            'tags' => ['Brand pass', 'Typography', 'Website rebuild'],
            'summary' => 'A Los Angeles cirque company whose site hid its own spectacle. We rebuilt it layer by layer until it looked like a circus poster, not a template.',
            'hero_image' => 'dreamworld-live-hero.jpg',
            'narrative' => <<<'MD'
                ## The starting point

                Dream World Cirque is a boutique aerial and cirque company in Los Angeles: contortionists, aerialists, fire and LED acts, and performers with backgrounds in Cirque du Soleil and professional ballet. Their old site, built years ago in Adobe Muse, told almost none of that. A carousel competed with the logo, the calls to action were buried, and the contact form had been quietly sending enquiries to an old mailbox for years.

                We started with an audit of the old site, then rebuilt it. Building fast with AI tools carries a specific risk: everything drifts towards the same defaults. The same fonts, the same gradients, the same centred hero with three icon cards underneath. The brand pass is how we stopped that from happening.

                ## Layer by layer

                - **Type.** When we asked for "distinctive" fonts, the suggestions were the same handful of elegant serifs that every AI-assisted redesign reaches for. We went the other way: a decorative display face with the character of a vintage circus poster for the hero, and a quiet sans for everything else, so the performers stay the focus.
                - **Fonts that actually load.** The audit turned up a real bug: the body and heading fonts were named in the CSS but never delivered, so most visitors saw a system fallback. It only looked right on machines that happened to have the fonts installed. We self-hosted the real files.
                - **Colour and hero.** The palette comes straight from the stage lighting in their own photos: purple, hot pink and amber. The hero is a muted, looping performance video under a brand-coloured overlay, tuned so the footage reads and the type stays legible.
                - **Details.** The real hand-drawn crest in the header, a subject-matched icon for every service instead of one generic arrow, and photo-led sections in place of a template grid.

                ## The result

                A site that looks like Dream World Cirque and no one else, built on a CMS the team can update directly, with an AI-assisted pipeline behind it that classifies and drafts replies to incoming enquiries. The gallery below shows the method: an illustrative "AI default" version of the site next to the real one, the type decision, and each layer applied in turn.
                MD,
            'images' => [
                ['brand-4-before-after.jpg', 'Left: an illustrative AI-default build. Right: the live Dream World Cirque site.'],
                ['brand-2-type.jpg', 'The type decision: the fonts AI tools reach for, against the one chosen for this brand.'],
                ['brand-3-layers.jpg', 'Layer by layer, from the AI default to the finished site (illustrative steps).'],
                ['brand-1-cover.jpg', 'Same content, made unmistakably theirs.'],
            ],
        ]);

        $this->caseStudy(1, $hackedSiteRecovery, [
            'slug' => 'independent-magazine-hacked-site-recovery',
            'title' => 'An independent magazine: hacked-site recovery',
            'client_name' => 'Independent magazine and online store (anonymised)',
            'external_link' => null,
            'tags' => ['Incident response', 'WordPress', 'Hardening'],
            'summary' => 'Customers saw a red "deceptive site" warning. Behind it: six weeks of hidden admin access. We traced it, cleaned it and locked it down.',
            'hero_image' => 'recovery-1-cover.jpg',
            'narrative' => <<<'MD'
                ## How it surfaced

                A customer of an independent magazine's online store reported a red "Deceptive Website Warning" when visiting the site. We started investigating the same day. The domain itself hadn't been hijacked; the warning came from a fake "security check" popup injected into the site's theme and into six published posts.

                ## What we found

                An attacker had logged in with a stolen admin password and spent about six weeks building ways back in:

                - **Six malicious plugins**, one of them hiding the attacker's accounts from the admin screen
                - **A login-stealing plugin** posing as an analytics add-on, quietly sending every admin login to an outside server
                - **Two hidden administrator accounts** and **two fake themes** that let the attacker run commands
                - **49 copies** of the injected popup and **13 scheduled tasks** that reinstalled parts of the infection after removal
                - A **1.1 GB full-site backup** sitting in a public folder where anyone could download it

                ## Cleaning it up, then checking again

                The first sweep removed the plugins, accounts, popup and scheduled tasks, and rotated the site's security keys. A second sweep the next day found the better-disguised wave: the fake themes and the login stealer. Attackers rarely leave just one way back in, so we kept looking. A deeper review of the server logs later found **two more backdoor files** in a folder the first sweeps hadn't covered. They were removed the same day, the attacker was blocked, and every password and key was changed.

                ## Locked down

                Plugin and theme uploads from the dashboard were switched off, code was blocked from running in upload folders, eight unused plugins were removed, paid downloads were moved out of the public web folder, and a daily integrity check now flags any unexpected change the next morning. A clean copy of the site was also rebuilt on new, isolated hosting. The final re-scan came back clean. Since then the site's security add-on has blocked more than 144,000 login attempts, about 300 a day, every one refused.

                ## What the client needed to do

                Our report also covered the decisions only the owners could make: rotating payment provider keys together on a short call, whether to notify customers, and adding a second owner to the payment accounts so the business no longer depends on one phone.
                MD,
            'images' => [
                ['recovery-2-findings.jpg', 'What one hacked site was hiding.'],
                ['recovery-3-timeline.jpg', 'Six weeks undetected, then a same-day response and follow-up log review.'],
                ['recovery-4-hardening.jpg', 'How the way in was closed, and kept closed.'],
            ],
        ]);
    }

    /** @param array<string, mixed> $attributes */
    private function package(Page $page, int $sortOrder, array $attributes): Package
    {
        $attributes['sample_document'] = $this->publish('packages', $attributes['sample_document']);

        return Package::query()->updateOrCreate(
            ['page_id' => $page->id, 'slug' => $attributes['slug']],
            [...$attributes, 'is_published' => true, 'sort_order' => $sortOrder],
        );
    }

    /** @param array<string, mixed> $attributes */
    private function caseStudy(int $sortOrder, Package $package, array $attributes): void
    {
        $images = $attributes['images'];
        unset($attributes['images']);
        $attributes['hero_image'] = $this->publish('case-studies', $attributes['hero_image']);

        $caseStudy = CaseStudy::query()->updateOrCreate(
            ['slug' => $attributes['slug']],
            [...$attributes, 'package_id' => $package->id, 'is_published' => true, 'sort_order' => $sortOrder],
        );

        $caseStudy->images()->delete();
        foreach ($images as $index => [$file, $altText]) {
            $caseStudy->images()->create([
                'image' => $this->publish('case-studies', $file),
                'alt_text' => $altText,
                'sort_order' => $index,
            ]);
        }
    }

    private function publish(string $directory, string $file): string
    {
        $path = $directory.'/'.$file;
        Storage::disk('public')->put($path, file_get_contents(database_path('seeders/content/'.$path)));

        return $path;
    }
}
