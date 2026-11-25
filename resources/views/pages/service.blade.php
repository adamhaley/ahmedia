<x-layouts.site :page="$page">
  <x-hero
    :ring-style="$page->heroRingStyle()"
    :eyebrow="$page->section_eyebrow"
    :title="$page->hero_heading ?: $page->title"
    :copy="$page->hero_subheading"
  />

  @php
    // Each token line names a section, `credentials` or `projects:workflow` say
    $segments = collect($page->bodySegments())->map(fn ($segment) => (object) [
      'markdown' => $segment->markdown,
      'section' => $segment->isToken() ? $segment->token.($segment->argument ? ':'.$segment->argument : '') : null,
    ]);
    $placed = $segments->pluck('section')->filter();
    $firstText = $segments->filter(fn ($segment) => $segment->section === null)->keys()->first();
    $sideImage = $page->hero_image ? \Illuminate\Support\Facades\Storage::disk('public')->url($page->hero_image) : null;
    $lastText = $segments->filter(fn ($segment) => $segment->section === null)->keys()->last();
  @endphp

  {{-- The body, with any sections its [[token]] lines place --}}
  @foreach ($segments as $index => $segment)
  @if ($segment->section)
  @include('pages.partials.service-section', ['section' => $segment->section])
  @elseif ($page->section_heading)
  <x-split-content
    stacked
    :heading="$loop->first ? $page->section_heading : null"
    :body="$segment->markdown"
    :bullets="$index === $lastText ? $page->bullets : []"
    :image="$index === $firstText ? $sideImage : null"
    :card="$index === $firstText ? $page->side_card : null"
    :image-alt="$page->hero_heading ?: $page->title"
  />
  @endif
  @endforeach

  {{-- Default spots for the sections the body doesn't place --}}
  @foreach (['credentials', 'projects', 'packages'] as $section)
  @unless ($placed->contains($section))
  @include('pages.partials.service-section', ['section' => $section])
  @endunless
  @endforeach

  <x-cta-band
    :eyebrow="$page->cta_eyebrow"
    :heading="$page->cta_heading"
    :copy="$page->cta_copy"
    :testimonial="$page->ctaTestimonial"
    :buttonLabel="$page->cta_button_label"
    buttonHref="/#contact"
  />
</x-layouts.site>
