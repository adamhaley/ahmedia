<x-layouts.site :page="$page">
  @if ($page->section_heading)
  <x-split-content
    :heading="$page->section_heading"
    :body="$page->body"
    :bullets="$page->bullets"
  />
  @endif

  <x-doppler-lab />

  <x-cta-band
    :eyebrow="$page->cta_eyebrow"
    :heading="$page->cta_heading"
    copy="Tell us what you're building - we'll tell you honestly whether AI is the right tool for it."
    :buttonLabel="$page->cta_button_label"
    buttonHref="/#contact"
  />
</x-layouts.site>
