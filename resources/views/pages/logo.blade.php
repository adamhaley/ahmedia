<x-layouts.site :page="$page">
  <x-doppler-lab
    :eyebrow="$page->section_eyebrow"
    :heading="$page->hero_heading ?: $page->title"
    :intro="$page->hero_subheading"
  />

  {{-- The story of the mark, after the lab so visitors meet the toy first. Blank lines split the body into paragraphs. --}}
  @if ($page->body || $page->section_heading)
  <section class="section">
    <div class="container logo-story">
      @if ($page->section_heading)
      <h2 class="section-title">{{ $page->section_heading }}</h2>
      @endif
      @foreach (preg_split('/\R{2,}/', trim((string) $page->body), flags: PREG_SPLIT_NO_EMPTY) as $paragraph)
      <p class="section-copy">{{ $paragraph }}</p>
      @endforeach
      @if (count($page->bullets ?? []))
      <ul class="bullet-list">
        @foreach ($page->bullets as $bullet)
        <li>{{ $bullet }}</li>
        @endforeach
      </ul>
      @endif
    </div>
  </section>
  @endif

  <x-cta-band
    :eyebrow="$page->cta_eyebrow"
    :heading="$page->cta_heading"
    copy="Tell us what you're building - we'll tell you honestly whether AI is the right tool for it."
    :buttonLabel="$page->cta_button_label"
    buttonHref="/#contact"
  />
</x-layouts.site>
