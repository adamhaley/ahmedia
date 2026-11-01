<x-layouts.site :page="$page">
  <x-hero
    :ring-style="$page->dopplerRingStyle"
    :eyebrow="$page->section_eyebrow"
    :title="$page->hero_heading ?: $page->title"
    :copy="$page->hero_subheading"
    :actions="[['label' => 'Start a Project', 'href' => '/#contact', 'variant' => 'primary']]"
  />

  @if ($page->section_heading)
  <x-split-content
    :heading="$page->section_heading"
    :body="$page->body"
    :bullets="$page->bullets"
  />
  @endif

  @if ($packages->isNotEmpty())
  <x-package-grid :packages="$packages" />
  @endif

  <x-cta-band
    :eyebrow="$page->cta_eyebrow"
    :heading="$page->cta_heading"
    copy="Tell us what you're building - we'll tell you honestly whether AI is the right tool for it."
    :buttonLabel="$page->cta_button_label"
    buttonHref="/#contact"
  />
</x-layouts.site>
