<?php

namespace App\Http\Controllers;

use App\Enums\PageTemplate;
use App\Models\Page;
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
            ...$this->templateData($page),
        ]);
    }

    /** @return array<string, mixed> */
    private function templateData(Page $page): array
    {
        return match ($page->template) {
            PageTemplate::Home => [
                'services' => Page::query()
                    ->where('template', PageTemplate::ServicesIndex)
                    ->with(['children' => fn ($query) => $query->where('is_published', true)])
                    ->first(),
            ],
            PageTemplate::ServicesIndex => [
                'page' => $page->load(['children' => fn ($query) => $query->where('is_published', true)]),
            ],
            default => [],
        };
    }
}
