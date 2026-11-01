<?php

namespace App\Http\Controllers;

use App\Data\DopplerRingSettings;
use App\Enums\PageTemplate;
use App\Models\CaseStudy;
use App\Models\FaqItem;
use App\Models\Page;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PageController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $page = Page::query()
            ->where('path', trim($request->path(), '/'))
            ->when(! $request->user(), fn ($query) => $query->where('is_published', true))
            ->with('parent')
            ->firstOrFail();

        return response()->view($page->template->view(), [
            'page' => $page,
            ...$this->templateData($page, $request),
        ]);
    }

    /** @return array<string, mixed> */
    private function templateData(Page $page, Request $request): array
    {
        return match ($page->template) {
            PageTemplate::Home => [
                'services' => Page::query()
                    ->where('template', PageTemplate::ServicesIndex)
                    ->with(['children' => fn ($query) => $query->where('is_published', true)])
                    ->first(),
                'caseStudiesPage' => Page::query()->where('template', PageTemplate::CaseStudiesIndex)->where('is_published', true)->first(),
                'caseStudies' => CaseStudy::query()->where('is_published', true)->orderBy('sort_order')->limit(3)->get(),
                'contactPage' => Page::query()->where('template', PageTemplate::Contact)->first(),
                'testimonials' => Testimonial::query()->active()->get(),
            ],
            PageTemplate::ServicesIndex => [
                'page' => $page->load(['children' => fn ($query) => $query->where('is_published', true)]),
            ],
            PageTemplate::Service => [
                'packages' => $page->packages()->where('is_published', true)->get(),
            ],
            PageTemplate::Faq => [
                'faqItems' => FaqItem::query()->orderBy('sort_order')->orderBy('id')->get(),
            ],
            PageTemplate::CaseStudiesIndex => [
                'caseStudies' => CaseStudy::query()->where('is_published', true)->orderBy('sort_order')->get(),
            ],
            // A shared lab link carries its arrangement in the query string (public/doppler-rings.js)
            PageTemplate::Logo => [
                'labSettings' => DopplerRingSettings::fromArray(['motion' => 'auto', ...array_filter($request->query(), is_string(...))]),
                'labFrozen' => $request->boolean('frozen'),
                'labTempo' => $request->float('tempo'),
            ],
            default => [],
        };
    }
}
