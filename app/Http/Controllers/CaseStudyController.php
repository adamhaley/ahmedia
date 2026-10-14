<?php

namespace App\Http\Controllers;

use App\Models\CaseStudy;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CaseStudyController extends Controller
{
    public function __invoke(Request $request, string $slug): Response
    {
        $caseStudy = CaseStudy::query()
            ->where('slug', $slug)
            ->when(! $request->user(), fn ($query) => $query->where('is_published', true))
            ->with(['images', 'package.page'])
            ->firstOrFail();

        return response()->view('case-studies.show', ['caseStudy' => $caseStudy]);
    }
}
