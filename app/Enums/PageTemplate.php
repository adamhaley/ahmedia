<?php

declare(strict_types=1);

namespace App\Enums;

use App\Data\DopplerRingSettings;
use App\Enums\Concerns\TranslatesLabels;
use App\Models\CaseStudy;
use App\Models\Credential;
use App\Models\FaqItem;
use App\Models\Page;
use App\Models\Post;
use App\Models\Project;
use App\Models\SiteSetting;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use PrismCms\Contracts\PageTemplate as PageTemplateContract;
use PrismCms\Enums\PageSection;
use PrismCms\Models\Page as PrismPage;
use PrismCms\Templates\DescribesPageTemplate;

enum PageTemplate: string implements PageTemplateContract
{
    use DescribesPageTemplate;
    use TranslatesLabels;

    case Home = 'home';
    case About = 'about';
    case Contact = 'contact';
    case ServicesIndex = 'services_index';
    case Service = 'service';
    case CaseStudiesIndex = 'case_studies_index';
    case Blog = 'blog';
    case Faq = 'faq';
    case Logo = 'logo';
    case Standard = 'standard';

    // Templates the site structure depends on; only freely creatable/deletable
    // templates (Service, Standard) may be applied or removed without care.
    public function isSystem(): bool
    {
        return ! in_array($this, [self::Service, self::Standard], true);
    }

    /**
     * The core editor sections each template uses. ahmedia still has its own page editor, so
     * this describes it for when it moves onto Prism CMS core's.
     *
     * @return list<PageSection>
     */
    public function sections(): array
    {
        return array_values(array_filter([
            PageSection::Hero,
            in_array($this, [self::Home, self::Logo], true) ? null : PageSection::HeroImage,
            PageSection::Content,
            in_array($this, [self::Home, self::About, self::Standard, self::Service, self::Logo], true) ? PageSection::Bullets : null,
            in_array($this, [self::Home, self::About, self::Standard, self::Service, self::Logo], true) ? PageSection::Buttons : null,
            $this === self::About ? PageSection::CircleImage : null,
            $this === self::Service ? PageSection::Card : null,
            in_array($this, [self::Home, self::Contact], true) ? null : PageSection::CallToAction,
        ]));
    }

    /** @return array<string, mixed> */
    public function data(PrismPage $page, Request $request): array
    {
        /** @var Page $page */
        return match ($this) {
            self::Home => [
                'services' => Page::query()
                    ->where('template', self::ServicesIndex)
                    ->with(['children' => fn ($query) => $query->where('is_published', true)])
                    ->first(),
                'caseStudiesPage' => Page::query()->where('template', self::CaseStudiesIndex)->where('is_published', true)->first(),
                'caseStudies' => CaseStudy::query()->where('is_published', true)->orderBy('sort_order')->limit(3)->get(),
                'contactPage' => Page::query()->where('template', self::Contact)->first(),
                'testimonials' => Testimonial::query()->active()->get(),
                'tagline' => SiteSetting::current()->footer_tagline,
            ],
            self::About => [
                'credentials' => Credential::query()->active()->get(),
                'settings' => SiteSetting::current(),
            ],
            self::ServicesIndex => [
                'page' => $page->load(['children' => fn ($query) => $query->where('is_published', true)]),
            ],
            self::Service => [
                'packages' => $page->packages()->where('is_published', true)->get(),
                'credentials' => $page->credentials()->active()->where('is_featured', false)->get(),
                'projectGroups' => $page->projects()->published()->with(['images', 'caseStudy'])->get()->groupBy(fn (Project $project): string => $project->type->value),
            ],
            self::Blog => [
                'posts' => Post::query()->published()->get(),
            ],
            self::Contact => [
                'settings' => SiteSetting::current(),
            ],
            self::Faq => [
                'faqItems' => FaqItem::query()->orderBy('sort_order')->orderBy('id')->get(),
            ],
            self::CaseStudiesIndex => self::caseStudiesIndexData($request),
            // A shared lab link carries its arrangement in the query string (public/doppler-rings.js)
            self::Logo => [
                'labSettings' => DopplerRingSettings::fromArray(['motion' => 'still', ...array_filter($request->query(), is_string(...))]),
                'labFrozen' => $request->boolean('frozen'),
                'labTempo' => $request->float('tempo'),
            ],
            default => [],
        };
    }

    /** @return array<string, mixed> */
    private static function caseStudiesIndexData(Request $request): array
    {
        $caseStudies = CaseStudy::query()->where('is_published', true)->orderBy('sort_order')->get();
        $activeTag = $request->string('tag')->toString() ?: null;

        return [
            'tags' => $caseStudies->pluck('tags')->flatten()->filter()->unique()->sort()->values(),
            'activeTag' => $activeTag,
            'caseStudies' => $activeTag
                ? $caseStudies->filter(fn (CaseStudy $caseStudy): bool => in_array($activeTag, $caseStudy->tags ?? [], true))->values()
                : $caseStudies,
            'testimonials' => Testimonial::query()->active()->get(),
        ];
    }
}
