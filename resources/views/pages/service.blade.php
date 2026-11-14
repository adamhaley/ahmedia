<x-layouts.site :page="$page">
  <x-hero
    :ring-style="$page->heroRingStyle()"
    :eyebrow="$page->section_eyebrow"
    :title="$page->hero_heading ?: $page->title"
    :copy="$page->hero_subheading"
    :actions="[['label' => 'Start a Project', 'href' => '/#contact', 'variant' => 'primary']]"
  />

  @php
    $segments = collect($page->bodySegments());
    $placed = $segments->pluck('section')->filter();
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
    copy="Tell us what you're building - we'll tell you honestly whether AI is the right tool for it."
    :buttonLabel="$page->cta_button_label"
    buttonHref="/#contact"
  />
</x-layouts.site>
