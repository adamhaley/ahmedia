<x-layouts.site :page="$page">
  <x-hero
    :ring-style="$page->heroRingStyle()"
    :eyebrow="$page->section_eyebrow"
    :title="$page->hero_heading ?: $page->title"
    :copy="$page->hero_subheading"
  />

  @if ($tags->count() > 1)
  <nav class="section tag-cloud-section" aria-label="Filter case studies by project type">
    <div class="container">
      <ul class="tag-cloud">
        <li><a href="{{ $page->url() }}#case-studies" @if (! $activeTag) aria-current="page" @endif>All projects</a></li>
        @foreach ($tags as $tag)
        <li><a href="{{ $page->url() }}?tag={{ urlencode($tag) }}#case-studies" @if ($tag === $activeTag) aria-current="page" @endif>{{ $tag }}</a></li>
        @endforeach
      </ul>
    </div>
  </nav>
  @endif

  <x-feature-grid
    id="case-studies"
    :title="$page->section_heading ?: 'Case Studies'"
    :copy="$page->body"
    :items="$caseStudies->map(fn ($caseStudy) => [
        'tags' => $caseStudy->tags,
        'title' => $caseStudy->title,
        'copy' => $caseStudy->summary,
        'href' => $caseStudy->narrative ? $caseStudy->url() : $caseStudy->external_link,
        'linkLabel' => $caseStudy->narrative ? 'Read the case study' : ($caseStudy->external_link ? 'View project' : null),
    ])->all()"
  />

  @if ($testimonials->isNotEmpty())
  <x-testimonials :testimonials="$testimonials" />
  @endif

  <x-cta-band
    :eyebrow="$page->cta_eyebrow"
    :heading="$page->cta_heading"
    copy="Tell us what you're building - we'll tell you honestly whether AI is the right tool for it."
    :buttonLabel="$page->cta_button_label"
    buttonHref="/#contact"
  />
</x-layouts.site>
